<?php
defined('C5_EXECUTE') or die("Access Denied.");

Loader::library('kintone/property_parser','kintone_form');

if (isset($message)) {
    echo '<p>'.$message.'</p>';
}

if (isset($fields)) {
    ?>
<form enctype="multipart/form-data" id="kintoneForm<?php echo intval($bID)?>" class="miniSurveyView" method="post" action="<?php echo $this->action('submit_form')?>">
    <table class="formBlockSurveyTable">
        <tbody>
        <?php
        foreach ($fields as $field) {
            $parser = new Kintone_Property_Parser($field);
            if (!$field->isMultiple()) {
                echo '<!--';
                var_dump($field->getProperties());
                echo '-->';
                ?>
                <tr>
                    <th><?php echo $parser->label(); ?></th>
                    <td><?php echo $parser->form(); ?></td>
                </tr>
                <?php
            } else {
                ?>
                <tr>
                    <th>&nbsp;</th>
                    <td>
                        <div class="subfield-container">
                            <?php
                            $val = $parser->getRequestedValue();
                            if (is_array($val)) {
                                foreach ($val as $i => $arr) {
                                    $isEmpty = true;
                                    foreach ($arr['value'] as $v) {
                                        if ($v['value']) {
                                            $isEmpty = false;
                                        }
                                    }
                                    if (!$isEmpty) {
                                        ?>
                                        <table>
                                            <tbody>
                                            <?php
                                            foreach ($field as $subfield) {
                                                $subparser = new Kintone_Property_Parser($subfield, $field->getFieldCode(), $i);
                                                ?>
                                                <tr>
                                                    <th><?php echo $subparser->label(); ?></th>
                                                    <td><?php echo $subparser->form(); ?></td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                        <?php
                                    }
                                }
                            }
                            ?>
                            <div class="toclone">
                                <table>
                                    <tbody>
                                    <?php
                                    $index = (is_array($val)) ? count($val) : 0;
                                    foreach ($field as $subfield) {
                                        $subparser = new Kintone_Property_Parser($subfield, $field->getFieldCode(), $index);
                                        ?>
                                        <tr>
                                            <th><?php echo $subparser->label(); ?></th>
                                            <td><?php echo $subparser->form(); ?></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>
                                <button class="clone">+</button>
                                <button class="delete">-</button>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php
            }
        }
        ?>
        </tbody>
    </table>
    <?php echo $form->submit('submit', t('Submit')); ?>
</form>
<?php } else { ?>
<p><?php echo t('Unable to connect to API.'); ?></p>
<?php }
