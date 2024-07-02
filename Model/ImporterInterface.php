<?php
namespace VML\CustomerImport\Model;


interface ImporterInterface
{
    public function import($source);
}
