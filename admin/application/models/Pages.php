<?php

    __autoloadDB('Db');

    class Pages extends Db
    {

        public function SavePage($dataForm)
        {
            global $mySession;
            $db = new Db();
            $chkQry = $db->runQuery("select * from " . PAGES1 . " where page_title='" . $dataForm['page_title'] . "'");
            if ($chkQry != "" and count($chkQry) > 0)
            {
                return 0;
            }
            else
            {
                //$dataPage['page_cat_id']=$dataForm['page_cat_id'];
                $dataPage['page_title'] = $dataForm['page_title'];
                $dataPage['page_content'] = $dataForm['page_content'];
                $dataPage['meta_keywords'] = $dataForm['meta_keywords'];
                $dataPage['meta_description'] = $dataForm['meta_description'];
                $dataPage['synonyms'] = $dataForm['synonyms'];

                if (isset($dataForm['page_parent']))
                    $dataPage['page_parent'] = $dataForm['page_parent'];

//                prd($dataPage);
                $db->save(PAGES1, $dataPage);
                return 1;
            }
        }

        public function UpdatePage($dataForm, $pageId)
        {
            global $mySession;
            $db = new Db();

            $chkQry = $db->runQuery("select * from " . PAGES1 . " where page_title='" . $dataForm['page_title'] . "' and page_id!='" . $pageId . "'");
            if ($chkQry != "" and count($chkQry) > 0)
            {
                return 0;
            }
            else
            {
//                prd($dataForm);
                //$dataUpdate['page_cat_id']=0;
//			echo $dataForm['page_content']; exit;
                $dataUpdate['page_title'] = strip_magic_slashes($dataForm['page_title']);
                $dataUpdate['page_content'] = strip_magic_slashes($dataForm['page_content']);
                $dataUpdate['meta_keywords'] = addslashes($dataForm['meta_keywords']);
                $dataUpdate['meta_description'] = addslashes($dataForm['meta_description']);
                $dataUpdate['synonyms'] = addslashes($dataForm['synonyms']);
//                prd($dataUpdate);
                $conditionUpdate = "page_id='" . $pageId . "'";
                $db->modify(PAGES1, $dataUpdate, $conditionUpdate);
                return 1;
            }
        }

        public function SaveCategory($dataForm)
        {
            global $mySession;
            $db = new Db();
            $chkQry = $db->runQuery("select * from " . PAGE_CAT . " where category_name='" . $dataForm['cat_name'] . "'");
            if ($chkQry != "" and count($chkQry) > 0)
            {
                return 0;
            }
            else
            {
                $dataInsert['category_name'] = $dataForm['cat_name'];
                $db->save(PAGE_CAT, $dataInsert);
                return 1;
            }
        }

        public function UpdateCategory($dataForm, $categoryId)
        {
            global $mySession;
            $db = new Db();
            $chkQry = $db->runQuery("select * from " . PAGE_CAT . " where category_name='" . $dataForm['cat_name'] . "' and cat_id!='" . $categoryId . "'");
            if ($chkQry != "" and count($chkQry) > 0)
            {
                return 0;
            }
            else
            {
                $dataUpdate['category_name'] = $dataForm['cat_name'];
                $conditionUpdate = "cat_id='" . $categoryId . "'";
                $db->modify(PAGE_CAT, $dataUpdate, $conditionUpdate);
                return 1;
            }
        }

    }

?>