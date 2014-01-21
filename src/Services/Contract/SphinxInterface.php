<?php
namespace Werkint\Bundle\SphinxBundle\Services\Contract;

use Werkint\Bundle\SphinxBundle\Services\Exception\WrongIndexException;

/**
 * SphinxInterface.
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
interface SphinxInterface
{
    /**
     * Search for the specified query string.
     *
     * @param string $query       The query string that we are searching for.
     * @param array  $indexes     The indexes to perform the search on.
     * @param array  $options     The options for the query.
     * @param bool   $escapeQuery Should the query string be escaped?
     *
     * @throws \RuntimeException
     * @throws WrongIndexException
     * @return array The results of the search.
     */
    public function search(
        $query,
        array $indexes,
        array $options = [],
        $escapeQuery = true
    );

    /**
     * Escape the supplied string.
     *
     * @param string $string The string to be escaped.
     * @return string The escaped string.
     */
    public function escapeString($string);

    /**
     * Set the desired match mode.
     *
     * @param int $mode The matching mode to be used.
     */
    public function setMatchMode($mode);

    /**
     * Set limits on the range and number of results returned.
     *
     * @param int $offset The number of results to seek past.
     * @param int $limit  The number of results to return.
     * @param int $max    The maximum number of matches to retrieve.
     * @param int $cutoff The cutoff to stop searching at.
     */
    public function setLimits($offset, $limit, $max = 0, $cutoff = 0);

    /**
     * Set weights for individual fields.  $weights should look like:
     *
     * $weights = array(
     *   'Normal field name' => 1,
     *   'Important field name' => 10,
     * );
     *
     * @param array $weights Array of field weights.
     */
    public function setFieldWeights(array $weights);

    /**
     * Set the desired search filter.
     *
     * @param string $attribute The attribute to filter.
     * @param array  $values    The values to filter.
     * @param bool   $exclude   Is this an exclusion filter?
     */
    public function setFilter($attribute, $values, $exclude = false);

    /**
     * Reset all previously set filters.
     */
    public function resetFilters();

    /**
     * Adds a query to a multi-query batch using current settings.
     *
     * @param string $query   The query string that we are searching for.
     * @param array  $indexes The indexes to perform the search on.
     */
    public function addQuery(
        $query,
        array $indexes
    );

    /**
     * Runs the currently batched queries, and returns the results.
     *
     * @return array The results of the queries.
     */
    public function runQueries();

    /**
     * Get Sphinx client
     *
     * @return \SphinxClient
     */
    public function getClient();
} 