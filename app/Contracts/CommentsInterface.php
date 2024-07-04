<?php
namespace App\Contracts;

interface CommentsInterface
{
    public function getComments($element_id);
    public function saveComment($data);
}