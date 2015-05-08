<?php
defined('C5_EXECUTE') or die("Access Denied.");

use Kintone\Request;
use Kintone\Form\Form;
use Kintone\Form\Field\FieldList;
use Kintone\Record\Record;
use Kintone\File\File;
use GuzzleHttp\Post\PostFile;

class KintoneFormBlockController extends BlockController
{
    protected $btTable = 'btKintoneForm';
    protected $btInterfaceWidth = "500";
    protected $btInterfaceHeight = "350";
    protected $btCacheBlockRecord = true;
	protected $btCacheBlockOutput = true;
	protected $btCacheBlockOutputOnPost = false;
	protected $btCacheBlockOutputForRegisteredUsers = true;
    protected $btCacheBlockOutputLifetime = 300;
    protected $btWrapperClass = 'ccm-ui';

    public function getBlockTypeDescription()
    {
        return t("Kintone Form");
    }

    public function getBlockTypeName()
    {
        return t("Kintone Form");
    }

    public function on_start()
    {
        $request = $this->getRequestObject();
        $form = new Form($request);
        $res = $form->getByID($this->appID);
        if ($res->isSuccess()) {
            $properties = $res->getProperties();
            $this->set('properties', $properties);
            $fields = new FieldList($properties);
            $this->set('fields', $fields);
        }
        $this->set('appID', $this->appID);
    }

    public function on_page_view()
    {
        $html = Loader::helper('html');
        $this->addHeaderItem($html->javascript('jquery-cloneya.min.js', 'kintone_form'));
    }

    public function getRequestObject()
    {
        return new Request($this->subDomain, $this->apiToken);
    }

    public function save($args)
    {
        $args['appID'] = intval($args['appID']);
        parent::save($args);
    }

    public function getJavaScriptStrings()
    {
        return array(
            'kintone-subdomain-required' => t('You must specify the subdomain.'),
            'kintone-apitoken-required' => t('You must specify the API Token.'),
            'kintone-appid-required' => t('You must specify the App ID.'),
        );
    }

    public function action_submit_form()
    {
        $ip = Loader::helper('validation/ip');
        if (!$ip->check()) {
            $this->set('error', $ip->getErrorMessage());
            return;
        }

        $post_data = $_POST['properties'];
        $file_data = $_FILES['properties'];
        $this->set('file_data', $file_data);

        if (!is_array($post_data)) {
            return;
        }

        if (is_array($file_data)) {
            $valf = Loader::helper('validation/file');
            $fh = Loader::helper('file');
            foreach ($file_data['tmp_name'] as $field => $filename) {
                if ($valf->file($filename['value'])) {
                    $name = $file_data['name'][$field]['value'];
                    //$name = $fh->sanitize($name);
                    $uploader = new File($this->getRequestObject());
                    $postfile = new PostFile('file', fopen($filename['value'], 'r'), $name);
                    $res = $uploader->postFile($postfile);
                    if ($res->isSuccess()) {
                        $post_data[$field] = array(
                            'type' => 'FILE',
                            'value' => array(
                                array(
                                    'fileKey' => $res->getFileKey(),
                                    'name' => $name
                                )
                            )
                        );
                    }
                }
            }
        }
        $this->set('post_data', $post_data);

        $record = new Record($this->getRequestObject());
        $res = $record->postRecord($this->appID, $post_data);
        $this->set('result', $res);

        if ($res->isSuccess()) {
            $this->set('message', t('Success'));
            $_POST['properties'] = null;
        } else {
            $this->set('error', t('Error %s: %s', $res->getStatusCode(), $res->getMessage()));
        }
    }

}
