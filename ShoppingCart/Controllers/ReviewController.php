<?php

namespace Controllers;

use Framework\BaseController;
use Framework\Common;
use Models\BindingModels\ReviewBindingModel;

class ReviewController extends BaseController
{
    /**
     * @Post
     * @Authorize
     * @Route("Review/add/{id:int}")
     * @param ReviewBindingModel $model
     * @throws \Exception
     */
    public function add(ReviewBindingModel $model){
        $message = Common::normalize($model->getMessage(), 'noescape|trim');
        $productId = $this->input->get(2);

        $this->db->prepare("
            SELECT id
            FROM users
            WHERE id = ? AND username = ?",
            [ $this->session->_login, $this->session->_username ]);

        $response = $this->db->execute()->fetchRowAssoc();
        $id = $response['id'];

        if ($id) {
            $this->db->prepare("
                SELECT name
                FROM products
                WHERE id = ?",
                [ $productId ]);

            $response = $this->db->execute()->fetchRowAssoc();

            if (!$response) {
                throw new \Exception("No product with id '$productId'", 404);
            }

            $this->db->prepare("
                INSERT INTO reviews (message, userId, productId)
                VALUES (?, ?, ?)",
                [ $message, $id, $productId ]
            )->execute();
        }

        $this->redirect("{$this->path}product/$productId/show");
    }

    /**
     * @Put
     * @Route("review/{id:int}/edit")
     * @Role("Moderator")
     * @param ReviewBindingModel $model
     */
    public function edit(ReviewBindingModel $model)
    {
        $id = $this->input->get(1);
        $this->db->prepare("
            SELECT productId
            FROM reviews
            WHERE id = ?",
            [ $id ]);

        $response = $this->db->execute()->fetchRowAssoc();
        $productId = Common::normalize($response['productId'], 'noescape|int');

        $this->db->prepare("
            UPDATE reviews
            SET message = ?
            WHERE id = ?",
            [ $model->getMessage(), $id ])->execute();

        $this->redirect("{$this->path}product/$productId/show");
    }

    /**
     * @Delete
     * @Route("review/{id:int}/delete")
     * @Role("Moderator")
     */
    public function remove()
    {
        $id = $this->input->get(1);
        $this->db->prepare("
            SELECT productId
            FROM reviews
            WHERE id = ?",
            [ $id ]);
        $response = $this->db->execute()->fetchRowAssoc();
        $productId = Common::normalize($response['productId'], 'noescape|int');

        $this->db->prepare("
            DELETE FROM reviews
            WHERE id = ?",
            [ $id ])->execute();

        $this->redirect("{$this->path}product/$productId/show");
    }
}