<?php


/**
 * This class defines the structure of the 'version_attribute' table.
 *
 *
 * This class was autogenerated by Propel 1.4.0-dev on:
 *
 * Tue Feb 25 13:44:12 2014
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    plugins.zeusCorePlugin.lib.model.map
 */
class VersionAttributeTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.zeusCorePlugin.lib.model.map.VersionAttributeTableMap';

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
		$this->setName('version_attribute');
		$this->setPhpName('VersionAttribute');
		$this->setClassname('VersionAttribute');
		$this->setPackage('plugins.zeusCorePlugin.lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addForeignKey('VERSION_ID', 'VersionId', 'INTEGER', 'version', 'ID', true, 11, null);
		$this->addColumn('NAME', 'Name', 'VARCHAR', false, 255, null);
		$this->addColumn('VALUE', 'Value', 'LONGVARCHAR', false, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('Version', 'Version', RelationMap::MANY_TO_ONE, array('version_id' => 'id', ), 'CASCADE', null);
	} // buildRelations()

} // VersionAttributeTableMap
