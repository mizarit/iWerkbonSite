<?php


/**
 * This class defines the structure of the 'mailing_subscription' table.
 *
 *
 * This class was autogenerated by Propel 1.4.2 on:
 *
 * Mon Jul  8 12:03:37 2013
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    plugins.zeusMailingPlugin.lib.model.map
 */
class MailingSubscriptionTableMap extends TableMap {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.zeusMailingPlugin.lib.model.map.MailingSubscriptionTableMap';

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
		$this->setName('mailing_subscription');
		$this->setPhpName('MailingSubscription');
		$this->setClassname('MailingSubscription');
		$this->setPackage('plugins.zeusMailingPlugin.lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addForeignKey('CONTACT_ID', 'ContactId', 'INTEGER', 'contact', 'ID', false, null, null);
		$this->addForeignKey('MAILING_ID', 'MailingId', 'INTEGER', 'mailing', 'ID', false, null, null);
		$this->addColumn('DATE', 'Date', 'TIMESTAMP', false, null, null);
		$this->addColumn('STATUS', 'Status', 'VARCHAR', false, 8, null);
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
    $this->addRelation('Contact', 'Contact', RelationMap::MANY_TO_ONE, array('contact_id' => 'id', ), null, null);
    $this->addRelation('Mailing', 'Mailing', RelationMap::MANY_TO_ONE, array('mailing_id' => 'id', ), null, null);
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

} // MailingSubscriptionTableMap
