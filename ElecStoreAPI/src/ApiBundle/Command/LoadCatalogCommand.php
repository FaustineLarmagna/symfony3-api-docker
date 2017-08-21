<?php

namespace ApiBundle\Command;

use ApiBundle\Entity\Category;
use ApiBundle\Entity\Product;
use ApiBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class LoadCatalogCommand
 *
 * @author f.larmagna@gmail.com
 */
class LoadCatalogCommand extends ContainerAwareCommand
{
    private $output;
    private $doctrine;
    private $em;

    protected function configure()
    {
        $this
            ->setName('elec:load-catalog')
            ->setDescription('Loads catalog data in database from json file.')
            ->setHelp('This command allows you to load a catalog of products in the database from a json file located in the files directory. Default name is electronic-catalog.json')
            ->addOption(
                'filename',
                'f',
                InputOption::VALUE_REQUIRED,
                'Json file name in files directory.',
                'electronic-catalog.json'
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $filename = $input->getOption('filename');
        $fileDirectory = $this->getContainer()->getParameter('files_directory');

        // check if file exists
        if (!is_file($fileDirectory.DIRECTORY_SEPARATOR.$filename)) {
            $this->output->writeln('<error>Specified file does not exist</error>');
            return;
        }

        $this->output->writeln([
            '---------------------------',
            'Loading catalog from file: '.$filename,
            '---------------------------',
            '',
        ]);

        $content = file_get_contents($fileDirectory.DIRECTORY_SEPARATOR.$filename);
        $content = json_decode($content, true);

        if (!empty($content['products'])) {
            $this->output->writeln([
                'Creating Products',
                '================='
            ]);

            $this->parseProducts($content['products']);
        }

        if (!empty($content['users'])) {
            $this->output->writeln([
                'Creating Users',
                '=============='
            ]);

            $this->parseUsers($content['users']);
        }

        $this->output->writeln([
            '',
            '<comment>Job done</comment>',
        ]);
    }

    /**
     * Takes the array of products decoded from the json file and
     * creates corresponding products and categories in database when necessary
     *
     * @param array $products
     */
    private function parseProducts(array $products)
    {
        // Initialize doctrine entity manager and repositories
        $doctrine = $this->getDoctrine();
        $em = $this->getEntityManager();
        $productRepository = $doctrine->getRepository(Product::class);
        $categoryRepository = $doctrine->getRepository(Category::class);
        $now = new \DateTime();

        foreach ($products as $product) {
            // check if product with given sku already exists in database
            $productObject = $productRepository->findOneBy([
                'sku' => $product['sku']
            ]);

            if ($productObject instanceof Product) {
                $this->output->writeln(sprintf(
                    '<info>Product with sku %s already exists in database.</info>',
                    $product['sku']
                ));
                continue;
            }

            // try to find category in $categories or to retrieve category from database
            $categoryName = $product['category'];
            $category = $categoryRepository->findOneBy([
                'name' => $categoryName
            ]);

            // if category can't be found, create it
            if (!$category instanceof Category) {
                $category = new Category();
                $category->setName($categoryName);
                $category->setCreatedAt($now);
                $category->setUpdatedAt($now);

                // register object and insert it in database
                $em->persist($category);
                $em->flush();

                $this->output->writeln(sprintf(
                    '<info>Category %s has been created in database.</info>',
                    $categoryName
                ));
            }

            // Create product
            $productObject = new Product();
            $productObject->setCategory($category);
            $productObject->setName($product['name']);
            $productObject->setSku($product['sku']);
            $productObject->setPrice($product['price']);
            $productObject->setQuantity($product['quantity']);
            $productObject->setCreatedAt($now);
            $productObject->setUpdatedAt($now);
            $em->persist($productObject);
            $em->flush();

            $this->output->writeln(sprintf(
                '<info>Product %s with sku %s has been created in database.</info>',
                $product['name'],
                $product['sku']
            ));
        }

        $this->output->writeln('');
    }

    /**
     * Takes the array of users decoded from the json file and
     * creates corresponding users in database when necessary
     *
     * @param array $users
     */
    private function parseUsers(array $users)
    {
        // Initialize doctrine entity manager and repositories
        $doctrine = $this->getDoctrine();
        $em = $this->getEntityManager();
        $userRepository = $doctrine->getRepository(User::class);
        $now = new \DateTime();

        foreach ($users as $userData) {
            // check if user with given email already exists in database
            $user = $userRepository->findOneBy([
                'email' => $userData['email']
            ]);

            if ($user instanceof User) {
                $this->output->writeln(sprintf(
                    '<info>User with email %s already exists in database.</info>',
                    $userData['email']
                ));
                continue;
            }

            // Create user
            $user = new User();
            $user->setUsername($userData['name']);
            $user->setEmail($userData['email']);
            $user->setApiKey(md5(sha1($userData['email'].$now->getTimestamp())));
            $user->setRoles(json_encode(['ROLE_API']));
            $user->setCreatedAt($now);
            $user->setUpdatedAt($now);
            $em->persist($user);
            $em->flush();

            $this->output->writeln(sprintf(
                '<info>User %s with email %s has been created in database.</info>',
                $userData['name'],
                $userData['email']
            ));
        }

        $this->output->writeln('');
    }

    /**
     * Returns instance of Doctrine
     */
    protected function getDoctrine(): Registry
    {
        return $this->doctrine ?: $this->getContainer()->get('doctrine');
    }

    /**
     * Returns instance of EntityManager
     */
    protected function getEntityManager(): EntityManager
    {
        return $this->em ?: $this->getDoctrine()->getEntityManager();
    }
}