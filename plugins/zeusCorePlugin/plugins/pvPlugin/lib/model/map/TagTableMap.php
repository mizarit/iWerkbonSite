<?php


/**
 * This class defines the structure of the 'tag' table.
 *
 *
 * This class was autogenerated by Propel 1.4.2 on:
 *
 * Mon Jul  2 13:23:08 2012
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    plugins.zeusCorePlugin.plugins.pvPlugin.lib.model.map
 */
class TagTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.zeusCorePlugin.plugins.pvPlugin.lib.model.map.TagTableMap';

	/**
	 * Initialize the table attributes, columns and validators
	 * Relations are not initialized by this method since they are lazy loaded
	 *
	 * @return     void
	 * @throws     PropelException
	 */
	public function initialize()
	{
	  // attributes
		$this->setName('tag');
		$this->setPhpName('Tag');
		$this->setClassname('Tag');
		$this->setPackage('plugins.zeusCorePlugin.plugins.pvPlugin.lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addColumn('TITLE', 'Title', 'VARCHAR', false, 255, null);
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('TagletterTag', 'TagletterTag', RelationMap::ONE_TO_MANY, array('id' => 'tag_id', ), null, null);
    $this->addRelation('LastminuteTag', 'LastminuteTag', RelationMap::ONE_TO_MANY, array('id' => 'tag_id', ), null, null);
	} // buildRelations()

	/**
	 * 
	 * Gets the list of behaviors registered for this table
	 * 
	 * @return array Associative array (name => parameters) of behaviors
	 */
	public function getBehaviors()
	{
		return array(
			'symfony' => array('form' => 'true', 'filter' => 'true', ),
			'symfony_behaviors' => array(),
		);
	} // getBehaviors()

} // TagTableMap
