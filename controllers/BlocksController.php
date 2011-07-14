<?php
/**
 * Blocks Controller
 * The blocks controller responsible for rendering both "static" and "dynamic" block content.
 * While the block helper could be used to render view templates from the /views folder, or any
 * folder underneath...For conventional and organizational reasons, "static" block templates should
 * live under the /views/blocks/static folder. 
 *
 * All block data for front-end use is accessed using the block helper.
 * (except when using the "ajax" method of the block helper and accessing the view method here)
 *
 * @author Tom Maiaroto
 * @website http://www.shift8creative.com
 * @modified 2010-06-10 15:13:50 
 * @created 2010-06-10 15:13:50 
 *
 */
namespace minerva\controllers;
use minerva\models\Block;
use li3_flash_message\extensions\storage\FlashMessage;
use li3_access\security\Access;
use \lithium\security\Auth;
use \lithium\storage\Session;
use \lithium\util\Set;
use minerva\libraries\util\Util;
use lithium\util\Inflector;

class BlocksController extends \minerva\controllers\MinervaController {
    
    public function view() {
		$path = func_get_args();
        
        if (empty($path)) {
            $path = array('example');
        }
        
        // this doesn't get any documents, it just checks access. the false "find_type" key is preventing a db query
        $document = $this->getDocument(array('action' => __METHOD__, 'request' => $this->request, 'find_type' => false));
        
        // getDocument() will return true or false depending on access rules. and it could redirect as well.
        if($document) {
           $this->render(array('template' => join('/', $path), 'layout' => 'blank'));
        }
    }
	
    // TODO: add caching
    public function read($url=null) {
		// get the page record (also within this record contains the library used, which is important)
		// TODO: make read conditions??
		return Block::find('first', array('conditions' => array('url' => $url, 'published' => true)));
    }
    
    public function index($document_type=null) {
		// all index() methods are the same so they are done in MinervaController, but we do need a little context as to where it's called from
        $this->calling_class = __CLASS__;
        $this->calling_method = __METHOD__;
        parent::index($document_type);
    }
	
    /** 
     * Create a Block record that has some basic fields that get stored in the database.
     * 
     * Blocks, like pages, can be created and associated to a library. This allows the library to have a "Block" model that
     * can apply filters and perform other actions much like Pages. It gives the library (Minerva plugin) an opportunity to
     * do a little more with block content. Typically you would expect a block to just have some HTML content and sit there
     * being very plain and boring. In other CMS' the idea of these simple blocks can access other parts of the CMS by allowing
     * PHP code to be set into the block and stored in the database. That's not typically a good approach because of when that
     * code actually gets executed. Thanks to Lithium's filter system and by optionally allowing a block to instantiate a
     * library block model class (to apply the filters) we can do much more. Queries can be altered, rendering options can
     * change and even complete other classes and code can be included to perform many operations (simple or complex) in an
     * elegant way.
     *
     * For example, think of a "Gallery Block" and what it may need to contain. Going to the url: site.com/blocks/create/gallery
     * would create a block under the gallery library's control. It would essentially "belong" to the gallery library.
     * You may have your gallery library's Block model add new fields to the block record. This could be all the paths to some
     * images somewhere or a reference to gallery record generated by the gallery library and stored elsewhere.
     * Then in your template you could loop through the images and display a gallery within a block.
     *
     * This is much more user friendly than having a big empty form textarea where someone who knew a little about development
     * would paste in, or type in from scratch, some PHP code to get the data that was required and then loop through and do
     * all the output right there because they didn't have the fields they needed on the block record. Again, this is where the
     * power and flexibility shine with MongoDB. Of course don't forget Lithium's filter system or the way Minerva is setup,
     * they all have to work together to pull off this flexibility.
     * 
    */
    public function create($document_type=null) {
		$this->calling_class = __CLASS__;
        $this->calling_method = __METHOD__;
        parent::create($document_type);
    }
    
    /**
     * Update a block record.
     * 
    */
    public function update($url=null) {	
		$this->calling_class = __CLASS__;
        $this->calling_method = __METHOD__;
        parent::update($url);
    }
	
    /** 
     *  Delete a block record.
     *  Plugins can apply filters within their Block model class in order to run filters for the delete.  
    */
    public function delete($url=null) {
		$this->calling_class = __CLASS__;
        $this->calling_method = __METHOD__;
        parent::delete($url);
    }
    
}
?>