<?php defined('C5_EXECUTE') or die("Access Denied.");

$uh = Loader::helper('concrete/urls');
$tool_url = $uh->getToolsURL('kintone/api/app', 'kintone_form');
echo $form->hidden('connect-tool-url', $tool_url);
?>
<div id="alert-message" class="alert error hide"></div>
<fieldset>
    <div class="control-group">
        <legend><?php echo t('API Connection'); ?></legend>
        <?php echo $form->label('subDomain', t('Subdomain')); ?>
        <div class="controls">
            <?php echo $form->text('subDomain', $subDomain); ?>
            <span class="help-block"><?php echo t('A subdomain of your sybozu.com'); ?></span>
        </div>
    </div>
    <div class="control-group">
        <?php echo $form->label('apiToken', t('API Token')); ?>
        <div class="controls">
            <?php echo $form->text('apiToken', $apiToken); ?>
            <span class="help-block"><?php echo t('API Token of your kintone app'); ?></span>
        </div>
    </div>
    <div class="control-group">
        <?php echo $form->label('appID', t('App ID')); ?>
        <div class="controls">
            <?php echo $form->text('appID', $appID); ?>
            <span class="help-block"><?php echo t('ID of your kintone app'); ?></span>
        </div>
    </div>
</fieldset>