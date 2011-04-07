<?php
namespace minerva\models;

use lithium\util\Inflector;
use lithium\core\Libraries;

class MinervaModel extends \lithium\data\Model {
    
    protected $_schema = array(
        '_id' => array('type' => 'id', 'form' => array('type' => 'hidden', 'label' => false)),
        'created' => array('type' => 'date', 'form' => array('type' => 'hidden', 'label' => false)),
		'document_type' => array('type' => 'string', 'form' => array('type' => 'hidden', 'label' => false)),
		'modified' => array('type' => 'date', 'form' => array('type' => 'hidden', 'label' => false))
    );
    public $search_schema = array();
    public $display_name = 'Model';
    static $document_access = array();
    static $access = array();
    public $document_type = '';
	protected $_meta = array(
		'locked' => true
	);
	
    public static function __init() {
		/**
		 * The following code will append a library Page model's $_schema and
		 * $validates properites to this Page model. $_schema can never be changed,
		 * only extended. $validates can be changed and extended, but if default
		 * field rules are left out, they will be used from this parent model.
		*/
		$class =  __CLASS__;
		
		// Use the library Page model's validation rules combined with the default (but the library gets priority) this way the default rules can be changed, but if left out will still be used (to help make things nicer)
		$class::_object()->validates = static::_object()->validates += $class::_object()->validates;
		
		// Same for the search schema, the library gets priority, but combine them.
		$class::_object()->search_schema = static::_object()->search_schema += $class::_object()->search_schema;
		
		// Replace any set display name for context
		$class::_object()->display_name = static::_object()->display_name;
		
		// Set the library name for this model
		$model_path = Libraries::path(get_class(static::_object()));
		$full_lib_path = LITHIUM_APP_PATH . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR;
		$library_string = substr($model_path, strlen($full_lib_path));
		$library_pieces = explode('/', $library_string);
		$class::_object()->library_name = $library_pieces[0];
		
		// Set the document type (for manualy set document_type values)
		$class::_object()->document_type = static::_object()->document_type;
		
        parent::__init();
    }
    
    /**
     * Get the display name for a model.
     * This helps to add a little bit of context for users.
     * For example, the create action template has a title "Create Page"
     * but if another page type uses that admin template, it would need
     * to be changed to something like "Create Blog Entry" for example.
     * The "display_name" property of each Page model changes that and
     * this method gets the value. Same goes for Users and other models.
     *
     * @return String
    */
    public function display_name() {
		$class =  __CLASS__;
		return $class::_object()->display_name;
    }
    
	/**
	 * Similiar to the display_name() method, this returns the library name
	 * for the current model.
	 *
	 * @return String
	*/
	public function library_name() {
		$class =  __CLASS__;
		return $class::_object()->library_name;
    }
	
	/**
	 * Get the document type for the model.
	 * Typically, the document type is the name of the library,
	 * but the model (that extends the corresponding minerva model)
	 * can manually set the document type as a property.
	 *
	 * This is useful for avoiding conflicts with other plugins.
	 * For example, there's two Page types in a system called "blog" ...
	 * You'd have to rename the library folder name, change some routing,
	 * but more importantly, you'd have different fields on the document, etc.
	 * so you need to change the document_type field.
	 *
	 * Note, if a document type can be null. It will use the base Minerva 
	 * models in that case meaning the schema will be limited.
	 *
	 * @return String
	*/
	public function document_type() {
		$class =  __CLASS__;
		return (isset($class::_object()->document_type)) ? $class::_object()->document_type:null;
    }
	
    /**
     * Returns the search schema for the model.
     * Note: If this model has been extended by another model then
     * the combined schema will be returned if that other model was
     * instantiated. The __init() method handles that.
     *
     * @param $field String The field for which to return the search schema for,
     * 			    if not provided, all fields will be returned
     * @return array
    */
    public function search_schema($field=null) {
	$class =  __CLASS__;
	$self = $class::_object();
	if (is_string($field) && $field) {
	    return isset($self->search_schema[$field]) ? $self->search_schema[$field] : array();
	}
	return $self->search_schema;
    }
    
    /**
     * Returns the proper model class to be using based on request.
     * For example, a PagesController "create" method will want to use the Page model, not this one.
     * While "Page" could be called directly (and should be in that case), it's not always clear which
     * model to use. Take for example a "blog" library that has a Page model. This is the model that
     * needs to be used because it has extra schema defined, etc.
     *
     * This is only the case for a few specific models for Minerva.
     *
     * @param $model_name The model name (should be page, user, or block)
     * @param $library_name The name of the library to search
     * @return class
    */
    public function getMinervaModel($model_name=null, $library_name=null) {
		$model = Libraries::locate('minerva_models', $library_name . '.' . $model_name);
		return (class_exists($model)) ? $model:__CLASS__;
    }
	
	/**
	 * Returns all minerva models
	 *
	 * @param $model_name The model name (should be page, user, or block)
	 * @return Array of classes
	*/
	public function getAllMinervaModels($model_name=null) {
		$models = Libraries::locate('minerva_models', $model_name);
		$models_array = $models;
		if(is_string($models)) {
			$models_array = array($models);
		}
		return $models_array;
	}
    
}
?>