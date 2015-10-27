<?php


/**
 * This class defines the structure of the 'lastminute_tag' table.
 *
 *
 * This class was autogenerated by Propel 1.4.2 on:
 *
 * Fri Jun 29 10:17:12 2012
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    plugins.zeusCorePlugin.plugins.pvLogin.lib.model.map
 */
class LastminuteTagTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.zeusCorePlugin.plugins.pvLogin.lib.model.map.LastminuteTagTableMap';

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
		$this->setName('lastminute_tag');
		$this->setPhpName('LastminuteTag');
		$this->setClassname('LastminuteTag');
		$this->setPackage('plugins.zeusCorePlugin.plugins.pvLogin.lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addForeignKey('TAG_ID', 'TagId', 'INTEGER', 'tag', 'ID', false, null, null);
		$this->addForeignKey('LASTMINUTE_ID', 'LastminuteId', 'INTEGER', 'lastminute', 'ID', false, null, null);
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('Tag', 'Tag', RelationMap::MANY_TO_ONE, array('tag_id' => 'id', ), null, null);
    $this->addRelation('Lastminute', 'Lastminute', RelationMap::MANY_TO_ONE, array('lastminute_id' => 'id', ), null, null);
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

} // LastminuteTagTableMap
