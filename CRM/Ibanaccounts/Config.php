<?php

/**
 * Singleton class to hold config settings for the iban module
 */

class CRM_Ibanaccounts_Config {
  
  protected static $_instance;
  
  protected $custom_groups = array();
  
  protected $custom_fields = array();

  protected $bicExtensionEnabled = false;
  protected $IbanMembershipEnabled = false;
  protected $IbanContributionshipEnabled = false;
  
  protected function __construct() {
    $this->custom_groups['IBAN'] = civicrm_api3('CustomGroup', 'getsingle', array('name' => 'IBAN'));

    //chek if IBAN Membership is enabled
    $result = civicrm_api3('CustomGroup', 'get', array('name' => 'IBAN_Membership', 'is_active' => 1));
    if ($result['count'] == 1){
      $this->custom_groups['IBAN_Membership'] = reset($result['values']);
      $this->IbanMembershipEnabled = true;
    }

    //check if IBAN Contribution is enabled
    $result = civicrm_api3('CustomGroup', 'get', array('name' => 'IBAN_Contribution', 'is_active' => 1));
    if ($result['count'] == 1){
      $this->custom_groups['IBAN_Contribution'] = reset($result['values']);
      $this->IbanContributionshipEnabled = true;
    }

    //load all the fields for every custom group
    foreach($this->custom_groups as $gname => $group) {
      $this->custom_fields[$gname] = array();
      $fields = civicrm_api3('CustomField', 'get', array('custom_group_id' => $group['id']));
      foreach($fields['values'] as $field) {
        $fname = $field['name'];
        $this->custom_fields[$gname][$fname] = $field;
      }
    }

    //check if org.project60.bic extension is installed
    $statuses = CRM_Extension_System::singleton()->getManager()->getStatuses();
    if (isset($statuses['org.project60.bic']) && $statuses['org.project60.bic'] == CRM_Extension_Manager::STATUS_INSTALLED) {
      $this->bicExtensionEnabled = true;
    }
  }

	/**
	 * Returns whether the user has access to the iban account custom data set.
	 */
	public static function accessToIbanAccounts() {
		try {
			$accessToCustomGroup = civicrm_api3('CustomGroup', 'getsingle', array('check_permissions' => 1, 'name' => 'IBAN'));
			return true;
		} catch (Exception $e) {
			return false;
		}
		return false;
	}
  
  /**
   * Singleton instanciated function
   * 
   * @return CRM_Ibanaccounts_Config
   */
  public static function singleton() {
    if (!self::$_instance) {
      self::$_instance = new CRM_Ibanaccounts_Config();
    }
    return self::$_instance;
  }

  /**
   * Returns wether the project 60 BIC extension is enabled
   *
   * The project 60 extension makes it possible to autofill
   * the BIC code based upon Iban
   *
   * @return bool
   */
  public function isProject60BICExtensionEnabled() {
    return $this->bicExtensionEnabled;
  }
  
  public function isIbanMembershipEnabled(){
    return $this->IbanMembershipEnabled;
  }

  public function isIbanContributionshipEnabled(){
    return $this->IbanContributionshipEnabled;
  }

  public function getIbanCustomGroupValue($field='id') {
    return $this->custom_groups['IBAN'][$field];
  }
  
  public function getIbanMembershipCustomGroupValue($field='id') {
    return $this->custom_groups['IBAN_Membership'][$field];
  }
  
  public function getIbanContributionCustomGroupValue($field='id') {
    return $this->custom_groups['IBAN_Contribution'][$field];
  }
  
  public function getIbanCustomFieldValue($field='id') {
    return $this->custom_fields['IBAN']['IBAN'][$field];
  }
  
  public function getBicCustomFieldValue($field='id') {
    return $this->custom_fields['IBAN']['BIC'][$field];
  }

  public function getTnvCustomFieldValue($field='id') {
    return $this->custom_fields['IBAN']['tnv'][$field];
  }
  
  public function getDateChangedCustomFieldValue($field='id') {
    return $this->custom_fields['IBAN']['datechanged'][$field];
  }

  public function getIbanMembershipCustomFieldValue($field='id') {
    return $this->custom_fields['IBAN_Membership']['IBAN'][$field];
  }
  
  public function getBicMembershipCustomFieldValue($field='id') {
    return $this->custom_fields['IBAN_Membership']['BIC'][$field];
  }

  public function getTnvMembershipCustomFieldValue($field='id') {
    return $this->custom_fields['IBAN_Membership']['tnv'][$field];
  }
  
  public function getIbanContributionCustomFieldValue($field='id') {
    return $this->custom_fields['IBAN_Contribution']['IBAN'][$field];
  }
  
  public function getBicContributionCustomFieldValue($field='id') {
    return $this->custom_fields['IBAN_Contribution']['BIC'][$field];
  }

  public function getTnvContributionCustomFieldValue($field='id') {
    return $this->custom_fields['IBAN_Contribution']['tnv'][$field];
  }

  /**
   * Return true when you want to enable validation of iban exist at other contact
   *
   * @return bool
   */
  public function isIbanOnlyAllowedOnce() {
    return false;
  }
  
}
