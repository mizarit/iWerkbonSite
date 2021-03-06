<?php

/**
 * Base class that represents a row from the 'coupon' table.
 *
 * 
 *
 * This class was autogenerated by Propel 1.4.2 on:
 *
 * Mon Jul  2 13:23:08 2012
 *
 * @package    plugins.zeusCorePlugin.plugins.pvPlugin.lib.model.om
 */
abstract class BaseCoupon extends BaseObject  implements Persistent {


	/**
	 * The Peer class.
	 * Instance provides a convenient way of calling static methods on a class
	 * that calling code may not be able to identify.
	 * @var        CouponPeer
	 */
	protected static $peer;

	/**
	 * The value for the lastminute_id field.
	 * @var        int
	 */
	protected $lastminute_id;

	/**
	 * The value for the consumer_id field.
	 * @var        int
	 */
	protected $consumer_id;

	/**
	 * The value for the consumer_email field.
	 * @var        string
	 */
	protected $consumer_email;

	/**
	 * The value for the consumer_phone field.
	 * @var        string
	 */
	protected $consumer_phone;

	/**
	 * The value for the consumer_name field.
	 * @var        string
	 */
	protected $consumer_name;

	/**
	 * The value for the lastminute_name field.
	 * @var        string
	 */
	protected $lastminute_name;

	/**
	 * The value for the lastminute_date field.
	 * @var        string
	 */
	protected $lastminute_date;

	/**
	 * The value for the code field.
	 * @var        string
	 */
	protected $code;

	/**
	 * The value for the check field.
	 * @var        string
	 */
	protected $check;

	/**
	 * The value for the id field.
	 * @var        int
	 */
	protected $id;

	/**
	 * @var        Lastminute
	 */
	protected $aLastminute;

	/**
	 * @var        Consumer
	 */
	protected $aConsumer;

	/**
	 * Flag to prevent endless save loop, if this object is referenced
	 * by another object which falls in this transaction.
	 * @var        boolean
	 */
	protected $alreadyInSave = false;

	/**
	 * Flag to prevent endless validation loop, if this object is referenced
	 * by another object which falls in this transaction.
	 * @var        boolean
	 */
	protected $alreadyInValidation = false;

	// symfony behavior
	
	const PEER = 'CouponPeer';

	/**
	 * Get the [lastminute_id] column value.
	 * 
	 * @return     int
	 */
	public function getLastminuteId()
	{
		return $this->lastminute_id;
	}

	/**
	 * Get the [consumer_id] column value.
	 * 
	 * @return     int
	 */
	public function getConsumerId()
	{
		return $this->consumer_id;
	}

	/**
	 * Get the [consumer_email] column value.
	 * 
	 * @return     string
	 */
	public function getConsumerEmail()
	{
		return $this->consumer_email;
	}

	/**
	 * Get the [consumer_phone] column value.
	 * 
	 * @return     string
	 */
	public function getConsumerPhone()
	{
		return $this->consumer_phone;
	}

	/**
	 * Get the [consumer_name] column value.
	 * 
	 * @return     string
	 */
	public function getConsumerName()
	{
		return $this->consumer_name;
	}

	/**
	 * Get the [lastminute_name] column value.
	 * 
	 * @return     string
	 */
	public function getLastminuteName()
	{
		return $this->lastminute_name;
	}

	/**
	 * Get the [optionally formatted] temporal [lastminute_date] column value.
	 * 
	 *
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw DateTime object will be returned.
	 * @return     mixed Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getLastminuteDate($format = 'Y-m-d H:i:s')
	{
		if ($this->lastminute_date === null) {
			return null;
		}


		if ($this->lastminute_date === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($this->lastminute_date);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($this->lastminute_date, true), $x);
			}
		}

		if ($format === null) {
			// Because propel.useDateTimeClass is TRUE, we return a DateTime object.
			return $dt;
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $dt->format('U'));
		} else {
			return $dt->format($format);
		}
	}

	/**
	 * Get the [code] column value.
	 * 
	 * @return     string
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * Get the [check] column value.
	 * 
	 * @return     string
	 */
	public function getCheck()
	{
		return $this->check;
	}

	/**
	 * Get the [id] column value.
	 * 
	 * @return     int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set the value of [lastminute_id] column.
	 * 
	 * @param      int $v new value
	 * @return     Coupon The current object (for fluent API support)
	 */
	public function setLastminuteId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->lastminute_id !== $v) {
			$this->lastminute_id = $v;
			$this->modifiedColumns[] = CouponPeer::LASTMINUTE_ID;
		}

		if ($this->aLastminute !== null && $this->aLastminute->getId() !== $v) {
			$this->aLastminute = null;
		}

		return $this;
	} // setLastminuteId()

	/**
	 * Set the value of [consumer_id] column.
	 * 
	 * @param      int $v new value
	 * @return     Coupon The current object (for fluent API support)
	 */
	public function setConsumerId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->consumer_id !== $v) {
			$this->consumer_id = $v;
			$this->modifiedColumns[] = CouponPeer::CONSUMER_ID;
		}

		if ($this->aConsumer !== null && $this->aConsumer->getId() !== $v) {
			$this->aConsumer = null;
		}

		return $this;
	} // setConsumerId()

	/**
	 * Set the value of [consumer_email] column.
	 * 
	 * @param      string $v new value
	 * @return     Coupon The current object (for fluent API support)
	 */
	public function setConsumerEmail($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->consumer_email !== $v) {
			$this->consumer_email = $v;
			$this->modifiedColumns[] = CouponPeer::CONSUMER_EMAIL;
		}

		return $this;
	} // setConsumerEmail()

	/**
	 * Set the value of [consumer_phone] column.
	 * 
	 * @param      string $v new value
	 * @return     Coupon The current object (for fluent API support)
	 */
	public function setConsumerPhone($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->consumer_phone !== $v) {
			$this->consumer_phone = $v;
			$this->modifiedColumns[] = CouponPeer::CONSUMER_PHONE;
		}

		return $this;
	} // setConsumerPhone()

	/**
	 * Set the value of [consumer_name] column.
	 * 
	 * @param      string $v new value
	 * @return     Coupon The current object (for fluent API support)
	 */
	public function setConsumerName($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->consumer_name !== $v) {
			$this->consumer_name = $v;
			$this->modifiedColumns[] = CouponPeer::CONSUMER_NAME;
		}

		return $this;
	} // setConsumerName()

	/**
	 * Set the value of [lastminute_name] column.
	 * 
	 * @param      string $v new value
	 * @return     Coupon The current object (for fluent API support)
	 */
	public function setLastminuteName($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->lastminute_name !== $v) {
			$this->lastminute_name = $v;
			$this->modifiedColumns[] = CouponPeer::LASTMINUTE_NAME;
		}

		return $this;
	} // setLastminuteName()

	/**
	 * Sets the value of [lastminute_date] column to a normalized version of the date/time value specified.
	 * 
	 * @param      mixed $v string, integer (timestamp), or DateTime value.  Empty string will
	 *						be treated as NULL for temporal objects.
	 * @return     Coupon The current object (for fluent API support)
	 */
	public function setLastminuteDate($v)
	{
		// we treat '' as NULL for temporal objects because DateTime('') == DateTime('now')
		// -- which is unexpected, to say the least.
		if ($v === null || $v === '') {
			$dt = null;
		} elseif ($v instanceof DateTime) {
			$dt = $v;
		} else {
			// some string/numeric value passed; we normalize that so that we can
			// validate it.
			try {
				if (is_numeric($v)) { // if it's a unix timestamp
					$dt = new DateTime('@'.$v, new DateTimeZone('UTC'));
					// We have to explicitly specify and then change the time zone because of a
					// DateTime bug: http://bugs.php.net/bug.php?id=43003
					$dt->setTimeZone(new DateTimeZone(date_default_timezone_get()));
				} else {
					$dt = new DateTime($v);
				}
			} catch (Exception $x) {
				throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
			}
		}

		if ( $this->lastminute_date !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($this->lastminute_date !== null && $tmpDt = new DateTime($this->lastminute_date)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$this->lastminute_date = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->modifiedColumns[] = CouponPeer::LASTMINUTE_DATE;
			}
		} // if either are not null

		return $this;
	} // setLastminuteDate()

	/**
	 * Set the value of [code] column.
	 * 
	 * @param      string $v new value
	 * @return     Coupon The current object (for fluent API support)
	 */
	public function setCode($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->code !== $v) {
			$this->code = $v;
			$this->modifiedColumns[] = CouponPeer::CODE;
		}

		return $this;
	} // setCode()

	/**
	 * Set the value of [check] column.
	 * 
	 * @param      string $v new value
	 * @return     Coupon The current object (for fluent API support)
	 */
	public function setCheck($v)
	{
		if ($v !== null) {
			$v = (string) $v;
		}

		if ($this->check !== $v) {
			$this->check = $v;
			$this->modifiedColumns[] = CouponPeer::CHECK;
		}

		return $this;
	} // setCheck()

	/**
	 * Set the value of [id] column.
	 * 
	 * @param      int $v new value
	 * @return     Coupon The current object (for fluent API support)
	 */
	public function setId($v)
	{
		if ($v !== null) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = CouponPeer::ID;
		}

		return $this;
	} // setId()

	/**
	 * Indicates whether the columns in this object are only set to default values.
	 *
	 * This method can be used in conjunction with isModified() to indicate whether an object is both
	 * modified _and_ has some values set which are non-default.
	 *
	 * @return     boolean Whether the columns in this object are only been set with default values.
	 */
	public function hasOnlyDefaultValues()
	{
		// otherwise, everything was equal, so return TRUE
		return true;
	} // hasOnlyDefaultValues()

	/**
	 * Hydrates (populates) the object variables with values from the database resultset.
	 *
	 * An offset (0-based "start column") is specified so that objects can be hydrated
	 * with a subset of the columns in the resultset rows.  This is needed, for example,
	 * for results of JOIN queries where the resultset row includes columns from two or
	 * more tables.
	 *
	 * @param      array $row The row returned by PDOStatement->fetch(PDO::FETCH_NUM)
	 * @param      int $startcol 0-based offset column which indicates which restultset column to start with.
	 * @param      boolean $rehydrate Whether this object is being re-hydrated from the database.
	 * @return     int next starting column
	 * @throws     PropelException  - Any caught Exception will be rewrapped as a PropelException.
	 */
	public function hydrate($row, $startcol = 0, $rehydrate = false)
	{
		try {

			$this->lastminute_id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
			$this->consumer_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
			$this->consumer_email = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
			$this->consumer_phone = ($row[$startcol + 3] !== null) ? (string) $row[$startcol + 3] : null;
			$this->consumer_name = ($row[$startcol + 4] !== null) ? (string) $row[$startcol + 4] : null;
			$this->lastminute_name = ($row[$startcol + 5] !== null) ? (string) $row[$startcol + 5] : null;
			$this->lastminute_date = ($row[$startcol + 6] !== null) ? (string) $row[$startcol + 6] : null;
			$this->code = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
			$this->check = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
			$this->id = ($row[$startcol + 9] !== null) ? (int) $row[$startcol + 9] : null;
			$this->resetModified();

			$this->setNew(false);

			if ($rehydrate) {
				$this->ensureConsistency();
			}

			// FIXME - using NUM_COLUMNS may be clearer.
			return $startcol + 10; // 10 = CouponPeer::NUM_COLUMNS - CouponPeer::NUM_LAZY_LOAD_COLUMNS).

		} catch (Exception $e) {
			throw new PropelException("Error populating Coupon object", $e);
		}
	}

	/**
	 * Checks and repairs the internal consistency of the object.
	 *
	 * This method is executed after an already-instantiated object is re-hydrated
	 * from the database.  It exists to check any foreign keys to make sure that
	 * the objects related to the current object are correct based on foreign key.
	 *
	 * You can override this method in the stub class, but you should always invoke
	 * the base method from the overridden method (i.e. parent::ensureConsistency()),
	 * in case your model changes.
	 *
	 * @throws     PropelException
	 */
	public function ensureConsistency()
	{

		if ($this->aLastminute !== null && $this->lastminute_id !== $this->aLastminute->getId()) {
			$this->aLastminute = null;
		}
		if ($this->aConsumer !== null && $this->consumer_id !== $this->aConsumer->getId()) {
			$this->aConsumer = null;
		}
	} // ensureConsistency

	/**
	 * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
	 *
	 * This will only work if the object has been saved and has a valid primary key set.
	 *
	 * @param      boolean $deep (optional) Whether to also de-associated any related objects.
	 * @param      PropelPDO $con (optional) The PropelPDO connection to use.
	 * @return     void
	 * @throws     PropelException - if this object is deleted, unsaved or doesn't have pk match in db
	 */
	public function reload($deep = false, PropelPDO $con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("Cannot reload a deleted object.");
		}

		if ($this->isNew()) {
			throw new PropelException("Cannot reload an unsaved object.");
		}

		if ($con === null) {
			$con = Propel::getConnection(CouponPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}

		// We don't need to alter the object instance pool; we're just modifying this instance
		// already in the pool.

		$stmt = CouponPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
		$row = $stmt->fetch(PDO::FETCH_NUM);
		$stmt->closeCursor();
		if (!$row) {
			throw new PropelException('Cannot find matching row in the database to reload object values.');
		}
		$this->hydrate($row, 0, true); // rehydrate

		if ($deep) {  // also de-associate any related objects?

			$this->aLastminute = null;
			$this->aConsumer = null;
		} // if (deep)
	}

	/**
	 * Removes this object from datastore and sets delete attribute.
	 *
	 * @param      PropelPDO $con
	 * @return     void
	 * @throws     PropelException
	 * @see        BaseObject::setDeleted()
	 * @see        BaseObject::isDeleted()
	 */
	public function delete(PropelPDO $con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("This object has already been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(CouponPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		try {
			$ret = $this->preDelete($con);
			// symfony_behaviors behavior
			foreach (sfMixer::getCallables('BaseCoupon:delete:pre') as $callable)
			{
			  if (call_user_func($callable, $this, $con))
			  {
			    $con->commit();
			
			    return;
			  }
			}

			if ($ret) {
				CouponPeer::doDelete($this, $con);
				$this->postDelete($con);
				// symfony_behaviors behavior
				foreach (sfMixer::getCallables('BaseCoupon:delete:post') as $callable)
				{
				  call_user_func($callable, $this, $con);
				}

				$this->setDeleted(true);
				$con->commit();
			} else {
				$con->commit();
			}
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Persists this object to the database.
	 *
	 * If the object is new, it inserts it; otherwise an update is performed.
	 * All modified related objects will also be persisted in the doSave()
	 * method.  This method wraps all precipitate database operations in a
	 * single transaction.
	 *
	 * @param      PropelPDO $con
	 * @return     int The number of rows affected by this insert/update and any referring fk objects' save() operations.
	 * @throws     PropelException
	 * @see        doSave()
	 */
	public function save(PropelPDO $con = null)
	{
		if ($this->isDeleted()) {
			throw new PropelException("You cannot save an object that has been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(CouponPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
		}
		
		$con->beginTransaction();
		$isInsert = $this->isNew();
		try {
			$ret = $this->preSave($con);
			// symfony_behaviors behavior
			foreach (sfMixer::getCallables('BaseCoupon:save:pre') as $callable)
			{
			  if (is_integer($affectedRows = call_user_func($callable, $this, $con)))
			  {
			    $con->commit();
			
			    return $affectedRows;
			  }
			}

			if ($isInsert) {
				$ret = $ret && $this->preInsert($con);
			} else {
				$ret = $ret && $this->preUpdate($con);
			}
			if ($ret) {
				$affectedRows = $this->doSave($con);
				if ($isInsert) {
					$this->postInsert($con);
				} else {
					$this->postUpdate($con);
				}
				$this->postSave($con);
				// symfony_behaviors behavior
				foreach (sfMixer::getCallables('BaseCoupon:save:post') as $callable)
				{
				  call_user_func($callable, $this, $con, $affectedRows);
				}

				CouponPeer::addInstanceToPool($this);
			} else {
				$affectedRows = 0;
			}
			$con->commit();
			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollBack();
			throw $e;
		}
	}

	/**
	 * Performs the work of inserting or updating the row in the database.
	 *
	 * If the object is new, it inserts it; otherwise an update is performed.
	 * All related objects are also updated in this method.
	 *
	 * @param      PropelPDO $con
	 * @return     int The number of rows affected by this insert/update and any referring fk objects' save() operations.
	 * @throws     PropelException
	 * @see        save()
	 */
	protected function doSave(PropelPDO $con)
	{
		$affectedRows = 0; // initialize var to track total num of affected rows
		if (!$this->alreadyInSave) {
			$this->alreadyInSave = true;

			// We call the save method on the following object(s) if they
			// were passed to this object by their coresponding set
			// method.  This object relates to these object(s) by a
			// foreign key reference.

			if ($this->aLastminute !== null) {
				if ($this->aLastminute->isModified() || $this->aLastminute->isNew()) {
					$affectedRows += $this->aLastminute->save($con);
				}
				$this->setLastminute($this->aLastminute);
			}

			if ($this->aConsumer !== null) {
				if ($this->aConsumer->isModified() || $this->aConsumer->isNew()) {
					$affectedRows += $this->aConsumer->save($con);
				}
				$this->setConsumer($this->aConsumer);
			}

			if ($this->isNew() ) {
				$this->modifiedColumns[] = CouponPeer::ID;
			}

			// If this object has been modified, then save it to the database.
			if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = CouponPeer::doInsert($this, $con);
					$affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
										 // should always be true here (even though technically
										 // BasePeer::doInsert() can insert multiple rows).

					$this->setId($pk);  //[IMV] update autoincrement primary key

					$this->setNew(false);
				} else {
					$affectedRows += CouponPeer::doUpdate($this, $con);
				}

				$this->resetModified(); // [HL] After being saved an object is no longer 'modified'
			}

			$this->alreadyInSave = false;

		}
		return $affectedRows;
	} // doSave()

	/**
	 * Array of ValidationFailed objects.
	 * @var        array ValidationFailed[]
	 */
	protected $validationFailures = array();

	/**
	 * Gets any ValidationFailed objects that resulted from last call to validate().
	 *
	 *
	 * @return     array ValidationFailed[]
	 * @see        validate()
	 */
	public function getValidationFailures()
	{
		return $this->validationFailures;
	}

	/**
	 * Validates the objects modified field values and all objects related to this table.
	 *
	 * If $columns is either a column name or an array of column names
	 * only those columns are validated.
	 *
	 * @param      mixed $columns Column name or an array of column names.
	 * @return     boolean Whether all columns pass validation.
	 * @see        doValidate()
	 * @see        getValidationFailures()
	 */
	public function validate($columns = null)
	{
		$res = $this->doValidate($columns);
		if ($res === true) {
			$this->validationFailures = array();
			return true;
		} else {
			$this->validationFailures = $res;
			return false;
		}
	}

	/**
	 * This function performs the validation work for complex object models.
	 *
	 * In addition to checking the current object, all related objects will
	 * also be validated.  If all pass then <code>true</code> is returned; otherwise
	 * an aggreagated array of ValidationFailed objects will be returned.
	 *
	 * @param      array $columns Array of column names to validate.
	 * @return     mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objets otherwise.
	 */
	protected function doValidate($columns = null)
	{
		if (!$this->alreadyInValidation) {
			$this->alreadyInValidation = true;
			$retval = null;

			$failureMap = array();


			// We call the validate method on the following object(s) if they
			// were passed to this object by their coresponding set
			// method.  This object relates to these object(s) by a
			// foreign key reference.

			if ($this->aLastminute !== null) {
				if (!$this->aLastminute->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aLastminute->getValidationFailures());
				}
			}

			if ($this->aConsumer !== null) {
				if (!$this->aConsumer->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aConsumer->getValidationFailures());
				}
			}


			if (($retval = CouponPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}



			$this->alreadyInValidation = false;
		}

		return (!empty($failureMap) ? $failureMap : true);
	}

	/**
	 * Retrieves a field from the object by name passed in as a string.
	 *
	 * @param      string $name name
	 * @param      string $type The type of fieldname the $name is of:
	 *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @return     mixed Value of field.
	 */
	public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = CouponPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		$field = $this->getByPosition($pos);
		return $field;
	}

	/**
	 * Retrieves a field from the object by Position as specified in the xml schema.
	 * Zero-based.
	 *
	 * @param      int $pos position in xml schema
	 * @return     mixed Value of field at $pos
	 */
	public function getByPosition($pos)
	{
		switch($pos) {
			case 0:
				return $this->getLastminuteId();
				break;
			case 1:
				return $this->getConsumerId();
				break;
			case 2:
				return $this->getConsumerEmail();
				break;
			case 3:
				return $this->getConsumerPhone();
				break;
			case 4:
				return $this->getConsumerName();
				break;
			case 5:
				return $this->getLastminuteName();
				break;
			case 6:
				return $this->getLastminuteDate();
				break;
			case 7:
				return $this->getCode();
				break;
			case 8:
				return $this->getCheck();
				break;
			case 9:
				return $this->getId();
				break;
			default:
				return null;
				break;
		} // switch()
	}

	/**
	 * Exports the object as an array.
	 *
	 * You can specify the key type of the array by passing one of the class
	 * type constants.
	 *
	 * @param      string $keyType (optional) One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                        BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM. Defaults to BasePeer::TYPE_PHPNAME.
	 * @param      boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns.  Defaults to TRUE.
	 * @return     an associative array containing the field names (as keys) and field values
	 */
	public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true)
	{
		$keys = CouponPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getLastminuteId(),
			$keys[1] => $this->getConsumerId(),
			$keys[2] => $this->getConsumerEmail(),
			$keys[3] => $this->getConsumerPhone(),
			$keys[4] => $this->getConsumerName(),
			$keys[5] => $this->getLastminuteName(),
			$keys[6] => $this->getLastminuteDate(),
			$keys[7] => $this->getCode(),
			$keys[8] => $this->getCheck(),
			$keys[9] => $this->getId(),
		);
		return $result;
	}

	/**
	 * Sets a field from the object by name passed in as a string.
	 *
	 * @param      string $name peer name
	 * @param      mixed $value field value
	 * @param      string $type The type of fieldname the $name is of:
	 *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @return     void
	 */
	public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = CouponPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->setByPosition($pos, $value);
	}

	/**
	 * Sets a field from the object by Position as specified in the xml schema.
	 * Zero-based.
	 *
	 * @param      int $pos position in xml schema
	 * @param      mixed $value field value
	 * @return     void
	 */
	public function setByPosition($pos, $value)
	{
		switch($pos) {
			case 0:
				$this->setLastminuteId($value);
				break;
			case 1:
				$this->setConsumerId($value);
				break;
			case 2:
				$this->setConsumerEmail($value);
				break;
			case 3:
				$this->setConsumerPhone($value);
				break;
			case 4:
				$this->setConsumerName($value);
				break;
			case 5:
				$this->setLastminuteName($value);
				break;
			case 6:
				$this->setLastminuteDate($value);
				break;
			case 7:
				$this->setCode($value);
				break;
			case 8:
				$this->setCheck($value);
				break;
			case 9:
				$this->setId($value);
				break;
		} // switch()
	}

	/**
	 * Populates the object using an array.
	 *
	 * This is particularly useful when populating an object from one of the
	 * request arrays (e.g. $_POST).  This method goes through the column
	 * names, checking to see whether a matching key exists in populated
	 * array. If so the setByName() method is called for that column.
	 *
	 * You can specify the key type of the array by additionally passing one
	 * of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
	 * BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
	 * The default key type is the column's phpname (e.g. 'AuthorId')
	 *
	 * @param      array  $arr     An array to populate the object from.
	 * @param      string $keyType The type of keys the array uses.
	 * @return     void
	 */
	public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = CouponPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setLastminuteId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setConsumerId($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setConsumerEmail($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setConsumerPhone($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setConsumerName($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setLastminuteName($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setLastminuteDate($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setCode($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setCheck($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setId($arr[$keys[9]]);
	}

	/**
	 * Build a Criteria object containing the values of all modified columns in this object.
	 *
	 * @return     Criteria The Criteria object containing all modified values.
	 */
	public function buildCriteria()
	{
		$criteria = new Criteria(CouponPeer::DATABASE_NAME);

		if ($this->isColumnModified(CouponPeer::LASTMINUTE_ID)) $criteria->add(CouponPeer::LASTMINUTE_ID, $this->lastminute_id);
		if ($this->isColumnModified(CouponPeer::CONSUMER_ID)) $criteria->add(CouponPeer::CONSUMER_ID, $this->consumer_id);
		if ($this->isColumnModified(CouponPeer::CONSUMER_EMAIL)) $criteria->add(CouponPeer::CONSUMER_EMAIL, $this->consumer_email);
		if ($this->isColumnModified(CouponPeer::CONSUMER_PHONE)) $criteria->add(CouponPeer::CONSUMER_PHONE, $this->consumer_phone);
		if ($this->isColumnModified(CouponPeer::CONSUMER_NAME)) $criteria->add(CouponPeer::CONSUMER_NAME, $this->consumer_name);
		if ($this->isColumnModified(CouponPeer::LASTMINUTE_NAME)) $criteria->add(CouponPeer::LASTMINUTE_NAME, $this->lastminute_name);
		if ($this->isColumnModified(CouponPeer::LASTMINUTE_DATE)) $criteria->add(CouponPeer::LASTMINUTE_DATE, $this->lastminute_date);
		if ($this->isColumnModified(CouponPeer::CODE)) $criteria->add(CouponPeer::CODE, $this->code);
		if ($this->isColumnModified(CouponPeer::CHECK)) $criteria->add(CouponPeer::CHECK, $this->check);
		if ($this->isColumnModified(CouponPeer::ID)) $criteria->add(CouponPeer::ID, $this->id);

		return $criteria;
	}

	/**
	 * Builds a Criteria object containing the primary key for this object.
	 *
	 * Unlike buildCriteria() this method includes the primary key values regardless
	 * of whether or not they have been modified.
	 *
	 * @return     Criteria The Criteria object containing value(s) for primary key(s).
	 */
	public function buildPkeyCriteria()
	{
		$criteria = new Criteria(CouponPeer::DATABASE_NAME);

		$criteria->add(CouponPeer::ID, $this->id);

		return $criteria;
	}

	/**
	 * Returns the primary key for this object (row).
	 * @return     int
	 */
	public function getPrimaryKey()
	{
		return $this->getId();
	}

	/**
	 * Generic method to set the primary key (id column).
	 *
	 * @param      int $key Primary key.
	 * @return     void
	 */
	public function setPrimaryKey($key)
	{
		$this->setId($key);
	}

	/**
	 * Sets contents of passed object to values from current object.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      object $copyObj An object of Coupon (or compatible) type.
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @throws     PropelException
	 */
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setLastminuteId($this->lastminute_id);

		$copyObj->setConsumerId($this->consumer_id);

		$copyObj->setConsumerEmail($this->consumer_email);

		$copyObj->setConsumerPhone($this->consumer_phone);

		$copyObj->setConsumerName($this->consumer_name);

		$copyObj->setLastminuteName($this->lastminute_name);

		$copyObj->setLastminuteDate($this->lastminute_date);

		$copyObj->setCode($this->code);

		$copyObj->setCheck($this->check);


		$copyObj->setNew(true);

		$copyObj->setId(NULL); // this is a auto-increment column, so set to default value

	}

	/**
	 * Makes a copy of this object that will be inserted as a new row in table when saved.
	 * It creates a new object filling in the simple attributes, but skipping any primary
	 * keys that are defined for the table.
	 *
	 * If desired, this method can also make copies of all associated (fkey referrers)
	 * objects.
	 *
	 * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
	 * @return     Coupon Clone of current object.
	 * @throws     PropelException
	 */
	public function copy($deepCopy = false)
	{
		// we use get_class(), because this might be a subclass
		$clazz = get_class($this);
		$copyObj = new $clazz();
		$this->copyInto($copyObj, $deepCopy);
		return $copyObj;
	}

	/**
	 * Returns a peer instance associated with this om.
	 *
	 * Since Peer classes are not to have any instance attributes, this method returns the
	 * same instance for all member of this class. The method could therefore
	 * be static, but this would prevent one from overriding the behavior.
	 *
	 * @return     CouponPeer
	 */
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new CouponPeer();
		}
		return self::$peer;
	}

	/**
	 * Declares an association between this object and a Lastminute object.
	 *
	 * @param      Lastminute $v
	 * @return     Coupon The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setLastminute(Lastminute $v = null)
	{
		if ($v === null) {
			$this->setLastminuteId(NULL);
		} else {
			$this->setLastminuteId($v->getId());
		}

		$this->aLastminute = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the Lastminute object, it will not be re-added.
		if ($v !== null) {
			$v->addCoupon($this);
		}

		return $this;
	}


	/**
	 * Get the associated Lastminute object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     Lastminute The associated Lastminute object.
	 * @throws     PropelException
	 */
	public function getLastminute(PropelPDO $con = null)
	{
		if ($this->aLastminute === null && ($this->lastminute_id !== null)) {
			$this->aLastminute = LastminutePeer::retrieveByPk($this->lastminute_id);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aLastminute->addCoupons($this);
			 */
		}
		return $this->aLastminute;
	}

	/**
	 * Declares an association between this object and a Consumer object.
	 *
	 * @param      Consumer $v
	 * @return     Coupon The current object (for fluent API support)
	 * @throws     PropelException
	 */
	public function setConsumer(Consumer $v = null)
	{
		if ($v === null) {
			$this->setConsumerId(NULL);
		} else {
			$this->setConsumerId($v->getId());
		}

		$this->aConsumer = $v;

		// Add binding for other direction of this n:n relationship.
		// If this object has already been added to the Consumer object, it will not be re-added.
		if ($v !== null) {
			$v->addCoupon($this);
		}

		return $this;
	}


	/**
	 * Get the associated Consumer object
	 *
	 * @param      PropelPDO Optional Connection object.
	 * @return     Consumer The associated Consumer object.
	 * @throws     PropelException
	 */
	public function getConsumer(PropelPDO $con = null)
	{
		if ($this->aConsumer === null && ($this->consumer_id !== null)) {
			$this->aConsumer = ConsumerPeer::retrieveByPk($this->consumer_id);
			/* The following can be used additionally to
			   guarantee the related object contains a reference
			   to this object.  This level of coupling may, however, be
			   undesirable since it could result in an only partially populated collection
			   in the referenced object.
			   $this->aConsumer->addCoupons($this);
			 */
		}
		return $this->aConsumer;
	}

	/**
	 * Resets all collections of referencing foreign keys.
	 *
	 * This method is a user-space workaround for PHP's inability to garbage collect objects
	 * with circular references.  This is currently necessary when using Propel in certain
	 * daemon or large-volumne/high-memory operations.
	 *
	 * @param      boolean $deep Whether to also clear the references on all associated objects.
	 */
	public function clearAllReferences($deep = false)
	{
		if ($deep) {
		} // if ($deep)

			$this->aLastminute = null;
			$this->aConsumer = null;
	}

	// symfony_behaviors behavior
	
	/**
	 * Calls methods defined via {@link sfMixer}.
	 */
	public function __call($method, $arguments)
	{
	  if (!$callable = sfMixer::getCallable('BaseCoupon:'.$method))
	  {
	    throw new sfException(sprintf('Call to undefined method BaseCoupon::%s', $method));
	  }
	
	  array_unshift($arguments, $this);
	
	  return call_user_func_array($callable, $arguments);
	}

} // BaseCoupon
