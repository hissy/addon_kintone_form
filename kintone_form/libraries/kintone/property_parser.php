<?php
defined('C5_EXECUTE') or die("Access Denied.");

use \PetrGrishin\ArrayAccess\ArrayAccess;

class Kintone_Property_Parser
{
    private $form;
    private $datetime;
    private $array;
    private $field;
    private $parentCode;
    private $index;

    public function __construct(
        \Kintone\Form\Field\FieldType $field,
        $parentCode = '',
        $index = 0
    ) {
        $this->form       = Loader::helper('form');
        $this->datetime   = Loader::helper('form/date_time');
        $this->array      = Loader::helper('array');
        $this->field      = $field;
        $this->parentCode = $parentCode;
        $this->index      = $index;
    }

    public function label()
    {
        $required = '';
        if ($this->field->isRequired()) {
            $required = '<small>'.t('Required').'</small>';
        }
        if (!$this->field->noLabel()) {
            return sprintf('<label for="%s">%s %s</label>',
                $this->getName(),
                $this->field->getLabel(),
                $required
            );
        }
    }

    public function form()
    {
        switch ($this->field->getFieldType()) {
            case 'SINGLE_LINE_TEXT':
            case 'CALC':
                return $this->text();
                break;
                break;
            case 'MULTI_LINE_TEXT':
            case 'RICH_TEXT':
                return $this->textarea();
                break;
            case 'NUMBER':
                return $this->number();
            case 'CHECK_BOX':
                return $this->checkbox();
            case 'RADIO_BUTTON':
                return $this->radio();
            case 'DROP_DOWN':
                return $this->select();
            case 'MULTI_SELECT':
                return $this->multipleselect();
            case 'FILE':
                return $this->file();
            case 'DATE':
                return $this->date();
            case 'TIME':
                return $this->time();
            case 'DATETIME':
                return $this->datetime();
            case 'LINK':
                return $this->link();
        }
    }

    public function text()
    {
        return sprintf(
            '<input type="text" name="%s" value="%s" %s/>',
            $this->getName(),
            $this->getValue(),
            $this->getRequired()
        );
    }

    public function textarea()
    {
        return sprintf(
            '<textarea name="%s" %s>%s</textarea>',
            $this->getName(),
            $this->getRequired(),
            $this->getValue()
        );
    }

    public function number()
    {
        return sprintf(
            '<input type="number" name="%s" value="%s" min="%s" max="%s" %s/>',
            $this->getName(),
            $this->getValue(),
            $this->field->getMinValue(),
            $this->field->getMaxValue(),
            $this->getRequired()
        );
    }

    public function checkbox()
    {
        $options = $this->field->getOptions();
        $values = $this->getValue();
        $html = '';
        if (is_array($options)) {
            foreach ($options as $option) {
                $html .= sprintf(
                    '<label><input type="checkbox" name="%s" value="%s" %s %s/> %s</label>',
                    $this->getName().'[]',
                    $option,
                    (in_array($option,$values)) ? 'checked="checked"' : '',
                    $this->getRequired(),
                    $option
                );
            }
        }
        return $html;
    }

    public function radio()
    {
        $options = $this->field->getOptions();
        $values = $this->getValue();
        $html = '';
        if (is_array($options)) {
            foreach ($options as $option) {
                $html .= sprintf(
                    '<label><input type="radio" name="%s" value="%s" %s %s/> %s</label>',
                    $this->getName().'[]',
                    $option,
                    (in_array($option,$values)) ? 'checked="checked"' : '',
                    $this->getRequired(),
                    $option
                );
            }
        }
        return $html;
    }

    public function select()
    {
        $options = $this->field->getOptions();
        $value = $this->getValue();
        $html = '';
        if (is_array($options)) {
            $html .= sprintf(
                '<select name="%s" %s>',
                $this->getName(),
                $this->getRequired()
            );
            $html .= sprintf('<option value="">%s</option>', t('Select'));
            foreach ($options as $option) {
                $html .= sprintf(
                    '<option value="%s" %s>%s</option>',
                    $option,
                    ($value == $option) ? 'selected="selected"' : '',
                    $option
                );
            }
            $html .= '</select>';
        }
        return $html;
    }

    public function selectmultiple()
    {
        // wip
    }

    public function file()
    {
        return sprintf(
            '<input type="file" name="%s" %s/>',
            $this->getName(),
            $this->getRequired()
        );
    }

    public function date()
    {
        return sprintf(
            '<input type="date" name="%s" value="%s" %s/>',
            $this->getName(),
            $this->getValue(),
            $this->getRequired()
        );
    }

    public function time()
    {
        return sprintf(
            '<input type="time" name="%s" value="%s" %s/>',
            $this->getName(),
            $this->getValue(),
            $this->getRequired()
        );
    }

    public function datetime()
    {
        return sprintf(
            '<input type="datetime" name="%s" value="%s" %s/>',
            $this->getName(),
            $this->getValue(),
            $this->getRequired()
        );
    }

    public function link()
    {
        $protocol = $this->field->getProtocol();
        if ($protocol == "WEB") {
            return sprintf('<input type="url" name="%s" value="%s" %s/>',
                $this->getName(),
                $this->getValue(),
                $this->getRequired()
            );
        } elseif ($protocol == "CALL") {
            return sprintf('<input type="tel" name="%s" value="%s" %s/>',
                $this->getName(),
                $this->getValue(),
                $this->getRequired()
            );
        } elseif ($protocol == "MAIL") {
            return sprintf('<input type="email" name="%s" value="%s" %s/>',
                $this->getName(),
                $this->getValue(),
                $this->getRequired()
            );
        } else {
            return $this->text();
        }
    }

    public function getRequired()
    {
        return ($this->field->isRequired()) ? 'required="required"' : '';
    }

    public function getName()
    {
        $name = 'properties';
        if ($this->parentCode) {
            $name .= '['.$this->parentCode.'][value]';
            $name .= '['.$this->index.'][value]';
        }
        $fieldCode = $this->field->getFieldCode();
        $name .= '['.h($fieldCode).'][value]';
        return $name;
    }

    public function getPath()
    {
        $path = array();
        $path[] = 'properties';
        if ($this->parentCode) {
            $path[] = $this->parentCode;
            $path[] = 'value';
            $path[] = $this->index;
            $path[] = 'value';
        }
        $path[] = $this->field->getFieldCode();
        $path[] = 'value';
        return implode('.', $path);
    }

    public function getRequestedValue()
    {
        $post = ArrayAccess::create($_POST);

        try {
            return $post->getValue($this->getPath());
        } catch (Exception $e) {}
    }

    public function getValue()
    {
        $val = $this->getRequestedValue();

        if (!isset($val) && !$this->field->isMultiple()) {
            $val = $this->field->getDefaultValue();
        }

        if (is_array($val)) {
            $array = array();
            foreach ($val as $v) {
                $array[] = str_replace('"', '&#34;', $v);
            }
            return $array;
        } elseif (isset($val)) {
            return str_replace('"', '&#34;', $val);
        }
    }
}