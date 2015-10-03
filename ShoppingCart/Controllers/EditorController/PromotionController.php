<?php

namespace Controllers\EditorController;

use Framework\BaseController;
use Framework\Common;
use Models\BindingModels\NameBindingModel;
use Models\BindingModels\PromotionBindingModel;
use Models\ViewModels\EditorViewModel\PromotionViewModel\AllViewModel;
use Models\ViewModels\EditorViewModel\PromotionViewModel\PromotionViewModel;

class PromotionController extends BaseController
{
    /**
     * @Post
     * @Route("editor/promotion/add")
     * @Role("Editor")
     * @param PromotionBindingModel $model
     * @throws \Exception
     */
    public function add(PromotionBindingModel $model)
    {
        $this->db->prepare("
            SELECT p.id
            FROM products p
            WHERE p.name LIKE ?",
            [ $model->getName() ]);

        $response = $this->db->execute()->fetchRowAssoc();

        if (!$response) {
            $name = $model->getName();
            throw new \Exception("No product with name '$name'!", 400);
        }

        $id = Common::normalize($response['id'], 'noescape|int');
        $this->db->prepare("
            INSERT INTO promotions (name, productId, percentage, endDate)
            VALUES (?, ?, ?, ?)",
            [
                $model->getName(),
                $id,
                $model->getPercentage(),
                $model->getDate()
            ])->execute();

        $this->redirect("{$this->path}editor");
    }

    /**
     * @Delete
     * @Route("editor/promotion/remove")
     * @Role("Editor")
     * @param NameBindingModel $model
     */
    public function remove(NameBindingModel $model)
    {
        $this->db->prepare("
            DELETE FROM promotions
            WHERE name LIKE ?",
            [
                $model->getName()
            ])->execute();

        $this->redirect("{$this->path}editor");
    }

    /**
     * @Get
     * @Route("editor/promotions/all")
     * @Role("Editor")
     */
    public function all()
    {
        $response = $this->db->prepare("
            SELECT pr.name, p.name as product, pr.percentage, pr.endDate
            FROM promotions pr
            JOIN products p
            ON pr.productId = p.id")->execute()->fetchAllAssoc();

        $promotions = [];

        foreach ($response as $promotion) {
            $promotions[] = new PromotionViewModel(
                $promotion['name'],
                $promotion['product'],
                Common::normalize($promotion['percentage'], 'noescape|double'),
                $promotion['endDate']
            );
        }

        $this->view->appendToLayout('meta', 'meta');
        $this->view->appendToLayout('header', 'header');
        $this->view->appendToLayout('body', new AllViewModel($promotions));
        $this->view->appendToLayout('footer', 'footer');
        $this->view->displayLayout('Layouts.Editor.home');
    }
}