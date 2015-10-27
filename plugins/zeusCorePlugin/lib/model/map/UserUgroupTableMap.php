<?php


/**
 * This class defines the structure of the 'user_ugroup' table.
 *
 *
 * This class was autogenerated by Propel 1.4.2 on:
 *
 * Tue Jul  3 11:06:52 2012
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    plugins.zeusCorePlugin.lib.model.map
 */
class UserUgroupTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.zeusCorePlugin.lib.model.map.UserUgroupTableMap';

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
		$this->setName('user_ugroup');
		$this->setPhpName('UserUgroup');
		$this->setClassname('UserUgroup');
		$this->setPackage('plugins.zeusCorePlugin.lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addForeignKey('USER_ID', 'UserId', 'INTEGER', 'user', 'ID', false, null, null);
		$this->addForeignKey('UGROUP_ID', 'UgroupId', 'INTEGER', 'ugroup', 'ID', false, null, null);
		$this->addColumn('SORTORDER', 'Sortorder', 'INTEGER', false, null, null);
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('User', 'User', RelationMap::MANY_TO_ONE, array('user_id' => 'id', ), null, null);
    $this->addRelation('Ugroup', 'Ugroup', RelationMap::MANY_TO_ONE, array('ugroup_id' => 'id', ), null, null);
    $this->addRelation('Ubac', 'Ubac', RelationMap::ONE_TO_MANY, array('id' => 'user_ugroup_id', ), null, null);
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

} // UserUgroupTableMap