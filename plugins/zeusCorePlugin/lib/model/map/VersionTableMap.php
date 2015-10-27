<?php


/**
 * This class defines the structure of the 'version' table.
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
class VersionTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.zeusCorePlugin.lib.model.map.VersionTableMap';

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
		$this->setName('version');
		$this->setPhpName('Version');
		$this->setClassname('Version');
		$this->setPackage('plugins.zeusCorePlugin.lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		$this->addColumn('VERSION', 'Version', 'INTEGER', false, null, null);
		$this->addColumn('OBJECT_ID', 'ObjectId', 'INTEGER', false, null, null);
		$this->addColumn('OBJECT', 'Object', 'VARCHAR', false, 255, null);
		$this->addColumn('MUTATION', 'Mutation', 'VARCHAR', false, 8, null);
		$this->addColumn('TITLE', 'Title', 'VARCHAR', false, 255, null);
		$this->addColumn('COMMENT', 'Comment', 'LONGVARCHAR', false, null, null);
		$this->addColumn('CREATED_BY', 'CreatedBy', 'VARCHAR', false, 255, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addColumn('CULTURE', 'Culture', 'VARCHAR', false, 7, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('VersionAttribute', 'VersionAttribute', RelationMap::ONE_TO_MANY, array('id' => 'version_id', ), 'CASCADE', null);
	} // buildRelations()

} // VersionTableMap
