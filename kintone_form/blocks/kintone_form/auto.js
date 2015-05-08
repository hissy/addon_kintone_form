ccmValidateBlockForm = function() {
	if ($("#subDomain").val() == '') {
		ccm_addError(ccm_t('kintone-subdomain-required'));
	}
	if ($("#apiToken").val() == '') {
		ccm_addError(ccm_t('kintone-apitoken-required'));
	}
	if ($("#appID").val() == '') {
		ccm_addError(ccm_t('kintone-appid-required'));
	}
	return false;
}
