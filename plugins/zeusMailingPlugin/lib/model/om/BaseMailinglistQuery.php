<?php


/**
 * Base class that represents a query for the 'mailinglist' table.
 *
 * 
 *
 * This class was autogenerated by Propel 1.6.4-dev on:
 *
 * Wed May 16 11:52:32 2012
 *
 * @method     MailinglistQuery orderByTitle($order = Criteria::ASC) Order by the title column
 * @method     MailinglistQuery orderByEmail($order = Criteria::ASC) Order by the email column
 * @method     MailinglistQuery orderByDefaulttemplate($order = Criteria::ASC) Order by the defaulttemplate column
 * @method     MailinglistQuery orderById($order = Criteria::ASC) Order by the id column
 *
 * @method     MailinglistQuery groupByTitle() Group by the title column
 * @method     MailinglistQuery groupByEmail() Group by the email column
 * @method     MailinglistQuery groupByDefaulttemplate() Group by the defaulttemplate column
 * @method     MailinglistQuery groupById() Group by the id column
 *
 * @method     MailinglistQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     MailinglistQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     MailinglistQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     MailinglistQuery leftJoinSubscription($relationAlias = null) Adds a LEFT JOIN clause to the query using the Subscription relation
 * @method     MailinglistQuery rightJoinSubscription($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Subscription relation
 * @method     MailinglistQuery innerJoinSubscription($relationAlias = null) Adds a INNER JOIN clause to the query using the Subscription relation
 *
 * @method     Mailinglist findOne(PropelPDO $con = null) Return the first Mailinglist matching the query
 * @method     Mailinglist findOneOrCreate(PropelPDO $con = null) Return the first Mailinglist matching the query, or a new Mailinglist object populated from the query conditions when no match is found
 *
 * @method     Mailinglist findOneByTitle(string $title) Return the first Mailinglist filtered by the title column
 * @method     Mailinglist findOneByEmail(string $email) Return the first Mailinglist filtered by the email column
 * @method     Mailinglist findOneByDefaulttemplate(string $defaulttemplate) Return the first Mailinglist filtered by the defaulttemplate column
 * @method     Mailinglist findOneById(int $id) Return the first Mailinglist filtered by the id column
 *
 * @method     array findByTitle(string $title) Return Mailinglist objects filtered by the title column
 * @method     array findByEmail(string $email) Return Mailinglist objects filtered by the email column
 * @method     array findByDefaulttemplate(string $defaulttemplate) Return Mailinglist objects filtered by the defaulttemplate column
 * @method     array findById(int $id) Return Mailinglist objects filtered by the id column
 *
 * @package    propel.generator.plugins.zeusMailingPlugin.lib.model.om
 */
abstract class BaseMailinglistQuery extends ModelCriteria
{
	
	/**
	 * Initializes internal state of BaseMailinglistQuery object.
	 *
	 * @param     string $dbName The dabase name
	 * @param     string $modelName The phpName of a model, e.g. 'Book'
	 * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
	 */
	public function __construct($dbName = 'propel', $modelName = 'Mailinglist', $modelAlias = null)
	{
		parent::__construct($dbName, $modelName, $modelAlias);
	}

	/**
	 * Returns a new MailinglistQuery object.
	 *
	 * @param     string $modelAlias The alias of a model in the query
	 * @param     Criteria $criteria Optional Criteria to build the query from
	 *
	 * @return    MailinglistQuery
	 */
	public static function create($modelAlias = null, $criteria = null)
	{
		if ($criteria instanceof MailinglistQuery) {
			return $criteria;
		}
		$query = new MailinglistQuery();
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
	 * @return    Mailinglist|array|mixed the result, formatted by the current formatter
	 */
	public function findPk($key, $con = null)
	{
		if ($key === null) {
			return null;
		}
		if ((null !== ($obj = MailinglistPeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
			// the object is alredy in the instance pool
			return $obj;
		}
		if ($con === null) {
			$con = Propel::getConnection(MailinglistPeer::DATABASE_NAME, Propel::CONNECTION_READ);
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
	 * @return    Mailinglist A model object, or null if the key is not found
	 */
	protected function findPkSimple($key, $con)
	{
		$sql = 'SELECT `TITLE`, `EMAIL`, `DEFAULTTEMPLATE`, `ID` FROM `mailinglist` WHERE `ID` = :p0';
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
			$obj = new Mailinglist();
			$obj->hydrate($row);
			MailinglistPeer::addInstanceToPool($obj, (string) $row[0]);
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
	 * @return    Mailinglist|array|mixed the result, formatted by the current formatter
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
	 * @return    MailinglistQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKey($key)
	{
		return $this->addUsingAlias(MailinglistPeer::ID, $key, Criteria::EQUAL);
	}

	/**
	 * Filter the query by a list of primary keys
	 *
	 * @param     array $keys The list of primary key to use for the query
	 *
	 * @return    MailinglistQuery The current query, for fluid interface
	 */
	public function filterByPrimaryKeys($keys)
	{
		return $this->addUsingAlias(MailinglistPeer::ID, $keys, Criteria::IN);
	}

	/**
	 * Filter the query on the title column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByTitle('fooValue');   // WHERE title = 'fooValue'
	 * $query->filterByTitle('%fooValue%'); // WHERE title LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $title The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    MailinglistQuery The current query, for fluid interface
	 */
	public function filterByTitle($title = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($title)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $title)) {
				$title = str_replace('*', '%', $title);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(MailinglistPeer::TITLE, $title, $comparison);
	}

	/**
	 * Filter the query on the email column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByEmail('fooValue');   // WHERE email = 'fooValue'
	 * $query->filterByEmail('%fooValue%'); // WHERE email LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $email The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    MailinglistQuery The current query, for fluid interface
	 */
	public function filterByEmail($email = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($email)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $email)) {
				$email = str_replace('*', '%', $email);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(MailinglistPeer::EMAIL, $email, $comparison);
	}

	/**
	 * Filter the query on the defaulttemplate column
	 *
	 * Example usage:
	 * <code>
	 * $query->filterByDefaulttemplate('fooValue');   // WHERE defaulttemplate = 'fooValue'
	 * $query->filterByDefaulttemplate('%fooValue%'); // WHERE defaulttemplate LIKE '%fooValue%'
	 * </code>
	 *
	 * @param     string $defaulttemplate The value to use as filter.
	 *              Accepts wildcards (* and % trigger a LIKE)
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    MailinglistQuery The current query, for fluid interface
	 */
	public function filterByDefaulttemplate($defaulttemplate = null, $comparison = null)
	{
		if (null === $comparison) {
			if (is_array($defaulttemplate)) {
				$comparison = Criteria::IN;
			} elseif (preg_match('/[\%\*]/', $defaulttemplate)) {
				$defaulttemplate = str_replace('*', '%', $defaulttemplate);
				$comparison = Criteria::LIKE;
			}
		}
		return $this->addUsingAlias(MailinglistPeer::DEFAULTTEMPLATE, $defaulttemplate, $comparison);
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
	 * @return    MailinglistQuery The current query, for fluid interface
	 */
	public function filterById($id = null, $comparison = null)
	{
		if (is_array($id) && null === $comparison) {
			$comparison = Criteria::IN;
		}
		return $this->addUsingAlias(MailinglistPeer::ID, $id, $comparison);
	}

	/**
	 * Filter the query by a related Subscription object
	 *
	 * @param     Subscription $subscription  the related object to use as filter
	 * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
	 *
	 * @return    MailinglistQuery The current query, for fluid interface
	 */
	public function filterBySubscription($subscription, $comparison = null)
	{
		if ($subscription instanceof Subscription) {
			return $this
				->addUsingAlias(MailinglistPeer::ID, $subscription->getMailinglistId(), $comparison);
		} elseif ($subscription instanceof PropelCollection) {
			return $this
				->useSubscriptionQuery()
				->filterByPrimaryKeys($subscription->getPrimaryKeys())
				->endUse();
		} else {
			throw new PropelException('filterBySubscription() only accepts arguments of type Subscription or PropelCollection');
		}
	}

	/**
	 * Adds a JOIN clause to the query using the Subscription relation
	 *
	 * @param     string $relationAlias optional alias for the relation
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    MailinglistQuery The current query, for fluid interface
	 */
	public function joinSubscription($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		$tableMap = $this->getTableMap();
		$relationMap = $tableMap->getRelation('Subscription');

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
			$this->addJoinObject($join, 'Subscription');
		}

		return $this;
	}

	/**
	 * Use the Subscription relation Subscription object
	 *
	 * @see       useQuery()
	 *
	 * @param     string $relationAlias optional alias for the relation,
	 *                                   to be used as main alias in the secondary query
	 * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
	 *
	 * @return    SubscriptionQuery A secondary query class using the current class as primary query
	 */
	public function useSubscriptionQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
	{
		return $this
			->joinSubscription($relationAlias, $joinType)
			->useQuery($relationAlias ? $relationAlias : 'Subscription', 'SubscriptionQuery');
	}

	/**
	 * Exclude object from result
	 *
	 * @param     Mailinglist $mailinglist Object to remove from the list of results
	 *
	 * @return    MailinglistQuery The current query, for fluid interface
	 */
	public function prune($mailinglist = null)
	{
		if ($mailinglist) {
			$this->addUsingAlias(MailinglistPeer::ID, $mailinglist->getId(), Criteria::NOT_EQUAL);
		}

		return $this;
	}

} // BaseMailinglistQuery