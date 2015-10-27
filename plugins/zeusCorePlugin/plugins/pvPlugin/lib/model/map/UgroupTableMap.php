<?php


/**
 * This class defines the structure of the 'ugroup' table.
 *
 *
 * This class was autogenerated by Propel 1.4.2 on:
 *
 * Mon Jul  2 13:23:06 2012
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    plugins.zeusCorePlugin.plugins.pvPlugin.lib.model.map
 */
class UgroupTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.zeusCorePlugin.plugins.pvPlugin.lib.model.map.UgroupTableMap';

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
		$this->setName('ugroup');
		$this->setPhpName('Ugroup');
		$this->setClassname('Ugroup');
		$this->setPackage('plugins.zeusCorePlugin.plugins.pvPlugin.lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addColumn('TITLE', 'Title', 'VARCHAR', false, 64, null);
		$this->addColumn('SINGULAR', 'Singular', 'BOOLEAN', false, null, null);
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('UserUgroup', 'UserUgroup', RelationMap::ONE_TO_MANY, array('id' => 'ugroup_id', ), null, null);
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

} // UgroupTableMap
