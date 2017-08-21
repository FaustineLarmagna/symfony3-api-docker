<?php

namespace ApiBundle\Controller;

use ApiBundle\Entity\Category;
use ApiBundle\Form\CategoryType;
use ApiBundle\Service\CategoryService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends FOSRestController
{
    private $categoryService;

    /**
     * Return CategoryService
     *
     * @return CategoryService
     */
    public function getCategoryService(): CategoryService
    {
        if (!$this->categoryService) {
            $this->categoryService = $this->get(CategoryService::class);
        }

        return $this->categoryService;
    }

    /**
     * Retrieve requested category
     *
     * @param integer $id
     * @return View
     */
    public function getCategoryAction(int $id): View
    {
        return $this->view(
            $this->getCategoryService()->get($id),
            Response::HTTP_OK
        );
    }
    
    /**
     * Retrieve all categories
     *
     * @return View
     */
    public function getCategoriesAction(): View
    {
        $categories = $this->getCategoryService()->getAll();

        if (empty($categories[0])) {
            return $this->view([
                    'status' => Response::HTTP_NOT_FOUND,
                    'message' => 'No category found'
                ], Response::HTTP_NOT_FOUND
            );
        }

        return $this->view(
            $categories,
            Response::HTTP_OK
        );
    }

    /**
     * Create new category
     *
     * @param Request $request
     * @return View
     */
    public function postCategoryAction(Request $request): View
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        // transforming json request in php array
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if ($form->isValid()) {
            try {
                $this->getCategoryService()->save($category);
            } catch (UniqueConstraintViolationException $e) {
                return $this->view([
                        'status' => Response::HTTP_BAD_REQUEST,
                        'message' => sprintf('Category %s already exists', $category->getName())
                    ], Response::HTTP_BAD_REQUEST
                );
            }

            return $this->view([
                    'status' => Response::HTTP_CREATED,
                    'message' => 'Category has been created'
                ], Response::HTTP_CREATED
            );
        }

        return $this->view([
                'status' => Response::HTTP_BAD_REQUEST,
                'message' => 'Category could not be created'
            ], Response::HTTP_BAD_REQUEST
        );
    }
}
