<?php

namespace Controllers\EditorController;

use Framework\BaseController;
use Models\BindingModels\ProductBindingModel\NameBindingModel;
use Models\BindingModels\RenameBindingModel;

class CategoryController extends BaseController
{
    /**
     * @Role("Editor")
     * @Post
     * @param NameBindingModel $model
     */
    public function add(NameBindingModel $model)
    {
        $this->db->prepare("
            INSERT INTO categories (name)
            VALUES (?)",
            [ $model->getName() ])->execute();

        $this->redirect("{$this->path}categories");
    }

    /**
     * @Role("Editor")
     * @Delete
     * @param NameBindingModel $model
     * @throws \Exception
     */
    public function remove(NameBindingModel $model)
    {
        $response = $this->db->prepare("
            SELECT id
            FROM categories
            WHERE name LIKE ?",
            [ $model->getName() ])
            ->execute()
            ->fetchRowAssoc();

        if (!$response) {
            throw new \Exception("No category '" . $model->getName() . " ' found!", 400);
        }

        $id = $response['id'];
        $response = $this->db->prepare("
            SELECT p.id
            FROM categories c
            JOIN products_categories pc
                ON c.id = pc.categoryId
            JOIN products p
                ON p.id = pc.productId
            WHERE categoryId = ?",
            [ $id ])
            ->execute()
            ->fetchRowAssoc();

        if ($response) {
            throw new \Exception("Category isn't empty! First, move all products!", 400);
        }

        $this->db->prepare("
            DELETE FROM categories
            WHERE name LIKE ?",
            [ $model->getName() ])->execute();

        $this->redirect("{$this->path}categories");
    }

    /**
     * @Role("Editor")
     * @Put
     * @param RenameBindingModel $model
     */
    public function rename(RenameBindingModel $model)
    {
        $this->db->prepare("
            UPDATE categories
            SET name = ?
            WHERE name LIKE ?",
            [
                $model->getNewName(),
                $model->getOldName()
            ])->execute();

        $this->redirect("{$this->path}categories");
    }
}