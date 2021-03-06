<?php


/**
 * This class defines the structure of the 'lastminute_config' table.
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
class LastminuteConfigTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.zeusCorePlugin.plugins.pvPlugin.lib.model.map.LastminuteConfigTableMap';

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
		$this->setName('lastminute_config');
		$this->setPhpName('LastminuteConfig');
		$this->setClassname('LastminuteConfig');
		$this->setPackage('plugins.zeusCorePlugin.plugins.pvPlugin.lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addColumn('KEY', 'Key', 'VARCHAR', false, 255, null);
		$this->addColumn('VALUE', 'Value', 'LONGVARCHAR', false, null, null);
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
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

} // LastminuteConfigTableMap
