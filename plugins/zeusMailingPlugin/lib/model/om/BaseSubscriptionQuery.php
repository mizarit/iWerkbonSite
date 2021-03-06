<?php


/**
 * Base class that represents a query for the 'subscription' table.
 *
 * 
 *
 * This class was autogenerated by Propel 1.6.4-dev on:
 *
 * Wed May 16 11:52:32 2012
 *
 * @method     SubscriptionQuery orderByMailinguserId($order = Criteria::ASC) Order by the mailinguser_id column
 * @method     SubscriptionQuery orderByMailinglistId($order = Criteria::ASC) Order by the mailinglist_id column
 * @method     SubscriptionQuery orderById($order = Criteria::ASC) Order by the id column
 *
 * @method     SubscriptionQuery groupByMailinguserId() Group by the mailinguser_id column
 * @method     SubscriptionQuery groupByMailinglistId() Group by the mailinglist_id column
 * @method     SubscriptionQuery groupById() Group by the id column
 *
 * @method     SubscriptionQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     SubscriptionQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     SubscriptionQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     SubscriptionQuery leftJoinMailinguser($relationAlias = null) Adds a LEFT JOIN clause to the query using the Mailinguser relation
 * @method     SubscriptionQuery rightJoinMailinguser($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Mailinguser relation
 * @method     SubscriptionQuery innerJoinMailinguser($relationAlias = null) Adds a INNER JOIN clause to the query using the Mailinguser relation
 *
 * @method     SubscriptionQuery leftJoinMailinglist($relationAlias = null) Adds a LEFT JOIN clause to the query using the Mailinglist relation
 * @method     SubscriptionQuery rightJoinMailinglist($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Mailinglist relation
 * @method     SubscriptionQuery innerJoinMailinglist($relationAlias = null) Adds a INNER JOIN clause to the query using the Mailinglist relation
 *
 * @method     Subscription findOne(PropelPDO $con = null) Return the first Subscription matching the query
 * @method     Subscription findOneOrCreate(PropelPDO $con = null) Return the first Subscription matching the query, or a new Subscription object populated from the query conditions when no match is found
 *
 * @method     Subscription findOneByMailinguserId(int $mailinguser_id) Return the first Subscription filtered by the mailinguser_id column
 * @method     Subscription findOneByMailinglistId(int $mailinglist_id) Return the first Subscription filtered by the mailinglist_id column
 * @method     Subscription findOneById(int $id) Return the first Subscription filtered by the id column
 *
 * @method     array findByMailinguserId(int $mailinguser_id) Return Subscription objects filtered by the mailinguser_id column
 * @method     array findByMailinglistId(int $mailinglist_id) Return Subscription objects filtered by the mailinglist_id column
 * @method     array findById(int $id) Return Subscription objects filtered by the id column
 *
 * @package    propel.generator.plugins.zeusMailingPlugin.lib.model.om
 */
abstract class BaseSubscriptionQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BaseSubscriptionQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'propel', $modelName = 'Subscription', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new SubscriptionQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    SubscriptionQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof SubscriptionQuery) {
			return $criteria;
		}
		$query = new SubscriptionQuery();
		if (null !== $modelAlias) {
			$query->setModelAlias($modelAlias);
		}
		if ($criteria instanceof Criteria) {
			$query->mergeWith($criteria);
		}
		return $query;
	}

	/**
	 * Find object by primary key.
	 * Propel uses the instance pool to skip the database if the object exists.
	 * Go fast if the query is untouched.
	 *
	 * <code>
	 * $obj  = $c->findPk(12, $con);
	 * </code>
	 *
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    Subscription|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = SubscriptionPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(SubscriptionPeer::DATABASE_NAME, Propel::CONNECTION_READ);
		}
		$this->basePreSelect($con);
		if ($this->formatter || $this->modelAlias || $this->with || $this->select
		 || $this->selectColumns || $this->asColumns || $this->selectModifiers
		 || $this->map || $this->having || $this->joins) {
			return $this->findPkComplex($key, $con);
		} else {
			return $this->findPkSimple($key, $con);
		}
	}

	/**
	 * Find object by primary key using raw SQL to go fast.
	 * Bypass doSelect() and the object formatter by using generated code.
	 *
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con A connection object
	 *
	 * @return    Subscription A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT `MAILINGUSER_ID`, `MAILINGLIST_ID`, `ID` FROM `subscription` WHERE `ID` = :p0';
		try {
			$stmt = $con->prepare($sql);
			$stmt->bindValue(':p0', $key, PDO::PARAM_INT);
			$stmt->execute();
		} catch (Exception $e) {
			Propel::log($e->getMessage(), Propel::LOG_ERR);
			throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
		}
		$obj = null;
		if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
			$obj = new Subscription();
			$obj->hydrate($row);
			SubscriptionPeer::addInstanceToPool($obj, (string) $row[0]);
		}
		$stmt->closeCursor();

		return $obj;
	}

	/**
	 * Find object by primary key.
	 *
	 * @param     mixed $key Primary key to use for the query
	 * @param     PropelPDO $con A connection object
	 *
	 * @return    Subscription|array|mixed the result, formatted by the current formatter
	 */
	protected function findPkComplex($key, $con)
	{
		// As the query uses a PK condition, no limit(1) is necessary.
		$criteria = $this->isKeepQuery() ? clone $this : $this;
		$stmt = $criteria
			->filterByPrimaryKey($key)
			->doSelect($con);
		return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
	}

	/**
	 * Find objects by primary key
	 * <code>
	 * $objs = $c->findPks(array(12, 56, 832), $con);
	 * </code>
	 * @param     array $keys Primary keys to use for the query
	 * @param     PropelPDO $con an optional connection object
	 *
	 * @return    PropelObjectCollection|array|mixed the list of results, formatted by the current formatter
	 */
	public function findPks($keys, $con = null)
	{
		if ($con === null) {
			$con = Propel::getConnection($this->getDbName(), Propel::CONNECTION_READ);
		}
		$this->basePreSelect($con);
		$criteria = $this->isKeepQuery() ? clone $this : $this;
		$stmt = $criteria
			->filterByPrimaryKeys($keys)
			->doSelect($con);
		return $criteria->getFormatter()->init($criteria)->format($stmt);
	}

	/**
	 * Filter the query by primary key
	 *
	 * @param     mixed $key Primary key to use for the query
	 *
	 * @return    SubscriptionQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(SubscriptionPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    SubscriptionQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(SubscriptionPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the mailinguser_id column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByMailinguserId(1234); // WHERE mailinguser_id = 1234
	 * $query->filterByMailinguserId(array(12, 34)); // WHERE mailinguser_id IN (12, 34)
	 * $query->filterByMailinguserId(array('min' => 12)); // WHERE mailinguser_id > 12
	 * </code>
	 *
	 * @see       filterByMailinguser()
	 *
	 * @param     mixed $mailinguserId The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    SubscriptionQuery The current query, for fluid interface
	 */
	public function filterByMailinguserId($mailinguserId = null, $comparison = null)
	{
		if (is_array($mailinguserId)) {
			$useMinMax = false;
			if (isset($mailinguserId['min'])) {
				$this->addUsingAlias(SubscriptionPeer::MAILINGUSER_ID, $mailinguserId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($mailinguserId['max'])) {
				$this->addUsingAlias(SubscriptionPeer::MAILINGUSER_ID, $mailinguserId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(SubscriptionPeer::MAILINGUSER_ID, $mailinguserId, $comparison);
	}

	/**
	 * Filter the query on the mailinglist_id column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByMailinglistId(1234); // WHERE mailinglist_id = 1234
	 * $query->filterByMailinglistId(array(12, 34)); // WHERE mailinglist_id IN (12, 34)
	 * $query->filterByMailinglistId(array('min' => 12)); // WHERE mailinglist_id > 12
	 * </code>
	 *
	 * @see       filterByMailinglist()
	 *
	 * @param     mixed $mailinglistId The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    SubscriptionQuery The current query, for fluid interface
	 */
	public function filterByMailinglistId($mailinglistId = null, $comparison = null)
	{
		if (is_array($mailinglistId)) {
			$useMinMax = false;
			if (isset($mailinglistId['min'])) {
				$this->addUsingAlias(SubscriptionPeer::MAILINGLIST_ID, $mailinglistId['min'], Criteria::GREATER_EQUAL);
				$useMinMax = true;
			}
			if (isset($mailinglistId['max'])) {
				$this->addUsingAlias(SubscriptionPeer::MAILINGLIST_ID, $mailinglistId['max'], Criteria::LESS_EQUAL);
				$useMinMax = true;
			}
			if ($useMinMax) {
				return $this;
			}
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
		}
		return $this->addUsingAlias(SubscriptionPeer::MAILINGLIST_ID, $mailinglistId, $comparison);
	}

	/**
	 * Filter the query on the id column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterById(1234); // WHERE id = 1234
	 * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
	 * $query->filterById(array('min' => 12)); // WHERE id > 12
	 * </code>
	 *
	 * @param     mixed $id The value to use as filter.
	 *              Use scalar values for equality.
	 *              Use array values for in_array() equivalent.
	 *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    SubscriptionQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(SubscriptionPeer::ID, $id, $comparison);
	}

	/**
	 * Filter the query by a related Mailinguser object
	 *
	 * @param     Mailinguser|PropelCollection $mailinguser The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    SubscriptionQuery The current query, for fluid interface
	 */
	public function filterByMailinguser($mailinguser, $comparison = null)
	{
		if ($mailinguser instanceof Mailinguser) {
			return $this
				->addUsingAlias(SubscriptionPeer::MAILINGUSER_ID, $mailinguser->getId(), $comparison);
		} elseif ($mailinguser instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(SubscriptionPeer::MAILINGUSER_ID, $mailinguser->toKeyValue('PrimaryKey', 'Id'), $comparison);
		} else {
			throw new PropelException('filterByMailinguser() only accepts arguments of type Mailinguser or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the Mailinguser relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    SubscriptionQuery The current query, for fluid interface
	 */
	public function joinMailinguser($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('Mailinguser');

		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		if ($previousJoin = $this->getPreviousJoin()) {
			$join->setPreviousJoin($previousJoin);
		}

		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'Mailinguser');
		}

		return $this;
	}

	/**
	 * Use the Mailinguser relation Mailinguser object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    MailinguserQuery A secondary query class using the current class as primary query
	 */
	public function useMailinguserQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinMailinguser($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Mailinguser', 'MailinguserQuery');
	}

	/**
	 * Filter the query by a related Mailinglist object
	 *
	 * @param     Mailinglist|PropelCollection $mailinglist The related object(s) to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    SubscriptionQuery The current query, for fluid interface
	 */
	public function filterByMailinglist($mailinglist, $comparison = null)
	{
		if ($mailinglist instanceof Mailinglist) {
			return $this
				->addUsingAlias(SubscriptionPeer::MAILINGLIST_ID, $mailinglist->getId(), $comparison);
		} elseif ($mailinglist instanceof PropelCollection) {
			if (null === $comparison) {
				$comparison = Criteria::IN;
			}
			return $this
				->addUsingAlias(SubscriptionPeer::MAILINGLIST_ID, $mailinglist->toKeyValue('PrimaryKey', 'Id'), $comparison);
		} else {
			throw new PropelException('filterByMailinglist() only accepts arguments of type Mailinglist or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the Mailinglist relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    SubscriptionQuery The current query, for fluid interface
	 */
	public function joinMailinglist($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('Mailinglist');

		// create a ModelJoin object for this join
		$join = new ModelJoin();
		$join->setJoinType($joinType);
		$join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
		if ($previousJoin = $this->getPreviousJoin()) {
			$join->setPreviousJoin($previousJoin);
		}

		// add the ModelJoin to the current object
		if($relationAlias) {
			$this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
			$this->addJoinObject($join, $relationAlias);
		} else {
			$this->addJoinObject($join, 'Mailinglist');
		}

		return $this;
	}

	/**
	 * Use the Mailinglist relation Mailinglist object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    MailinglistQuery A secondary query class using the current class as primary query
	 */
	public function useMailinglistQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinMailinglist($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Mailinglist', 'MailinglistQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     Subscription $subscription Object to remove from the list of results
	 *
	 * @return    SubscriptionQuery The current query, for fluid interface
	 */
	public function prune($subscription = null)
	{
		if ($subscription) {
			$this->addUsingAlias(SubscriptionPeer::ID, $subscription->getId(), Criteria::NOT_EQUAL);
		}

		return $this;
	}

} // BaseSubscriptionQuery