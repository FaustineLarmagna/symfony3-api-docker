<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\Product;
use ApiBundle\Form\ProductType;
use ApiBundle\Service\ProductService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends FOSRestController
{
    private $productService;

    /**
     * @return ProductService
     */
    public function getProductService(): ProductService
    {
        if (!$this->productService) {
            $this->productService = $this->get(ProductService::class);
        }

        return $this->productService;
    }

    /**
     * Retrieve requested product
     *
     * @param integer $id
     * @return View
     */
    public function getProductAction(int $id): View
    {
        return $this->view(
            $this->getProductService()->get($id),
            Response::HTTP_OK
        );
    }

    /**
     * Retrieve all products
     *
     * @return View
     */
    public function getProductsAction(): View
    {
        $products = $this->getProductService()->getAll();

        if (empty($products[0])) {
            return $this->view([
                    'code' => Response::HTTP_NO_CONTENT,
                    'message' => 'No product found'
                ], Response::HTTP_NO_CONTENT
            );
        }

        $view = $this->view(
            $products,
            Response::HTTP_OK
        );
        $context = new Context();
        $context->enableMaxDepth();
        $view->setContext($context);

        return $view;
    }

    /**
     * Create new product
     *
     * @param Request $request
     * @return View
     */
    public function postProductAction(Request $request): View
    {
        $product = new Product();

        return $this->editProduct($request, $product);
    }

    /**
     * Edit product
     *
     * @param Request $request
     * @param integer $id
     * @return View
     */
    public function putProductAction(Request $request, int $id): View
    {
        $product = $this->getProductService()->get($id);

        return $this->editProduct($request, $product, false);
    }

    /**
     * Validate request data with ProductType form
     * Save product in database
     *
     * @param Request $request
     * @param Product $product
     * @param boolean $creation
     * @return View
     */
    public function editProduct(Request $request, Product $product, bool $creation = true): View
    {
        $form = $this->createForm(ProductType::class, $product);
        $code = $creation ? Response::HTTP_CREATED : Response::HTTP_OK;
        $action = $creation ? 'created' : 'updated';

        // transforming json request in php array
        $data = json_decode($request->getContent(), true);
        $form->submit($data, false);

        if ($form->isValid()) {
            try {
                // save product in database
                $this->getProductService()->save($product);
            } catch (UniqueConstraintViolationException $e) {
                return $this->view([
                    'code' => Response::HTTP_BAD_REQUEST,
                    'message' => sprintf('Product with sku %s already exists', $product->getSku())
                ], Response::HTTP_BAD_REQUEST
                );
            }

            return $this->view([
                    'code' => $code,
                    'message' => 'Product has been '.$action
                ], $code
            );
        }

        // Invalid data
        return $this->view([
                'code' => Response::HTTP_BAD_REQUEST,
                'message' => 'Invalid request: Product could not be '.$action
            ], Response::HTTP_BAD_REQUEST
        );
    }

    /**
     * Remove product from database
     *
     * @param integer $id
     * @return View
     */
    public function deleteProductAction(int $id): View
    {
        $product = $this->getProductService()->get($id);

        $this->getProductService()->delete($product);

        return $this->view([
                'code' => Response::HTTP_OK,
                'message' => 'Product has been deleted'
            ], Response::HTTP_OK
        );
    }
}
