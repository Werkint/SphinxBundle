<?php
namespace Werkint\Bundle\SphinxBundle\Services;

use Werkint\Bundle\SphinxBundle\Services\Contract\SphinxInterface;

/**
 * Sphinx.
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class Sphinx implements
    SphinxInterface
{
    protected $host;
    protected $port;
    protected $socket;
    protected $indexes;
    protected $sphinx;

    /**
     * Constructor.
     *
     * @param string $host    The server's host name/IP.
     * @param string $port    The port that the server is listening on.
     * @param string $socket  The UNIX socket that the server is listening on.
     * @param array  $indexes The list of indexes that can be used.
     */
    public function __construct(
        $host = 'localhost',
        $port = '9312',
        $socket = null,
        array $indexes = []
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->socket = $socket;
        $this->indexes = $indexes;

        $this->sphinx = new \SphinxClient();
        if ($this->socket !== null) {
            $this->sphinx->setServer($this->socket);
        } else {
            $this->sphinx->setServer($this->host, $this->port);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getClient()
    {
        return $this->sphinx;
    }

    /**
     * {@inheritdoc}
     */
    public function search(
        $query,
        array $indexes,
        array $options = [],
        $escapeQuery = true
    ) {
        if ($escapeQuery) {
            $query = $this->sphinx->escapeString($query);
        }

        // Index list
        $indexNames = [];
        foreach ($indexes as $label) {
            if (!isset($this->indexes[$label])) {
                throw new Exception\WrongIndexException('Wrong index label: ' . $label);
            }
            $indexNames[] = $this->indexes[$label];
        }
        if (empty($indexNames)) {
            throw new Exception\WrongIndexException('No valid indexes specified');
        }

        // Set the offset and limit for the returned results.
        if (isset($options['result_offset']) && isset($options['result_limit'])) {
            $this->sphinx->setLimits($options['result_offset'], $options['result_limit']);
        }

        // Weight the individual fields.
        if (isset($options['field_weights'])) {
            $this->sphinx->setFieldWeights($options['field_weights']);
        }

        // Perform the query.
        $indexNames = join(' ', $indexNames);
        $results = $this->sphinx->query($query, $indexNames);
        if ($results['status'] !== SEARCHD_OK) {
            throw new \RuntimeException(sprintf(
                'Searching indexes "%s" for "%s" failed with error "%s".',
                $indexNames,
                $query,
                $this->sphinx->getLastError()
            ));
        }

        return $results;
    }

    /**
     * {@inheritdoc}
     */
    public function escapeString($string)
    {
        return $this->sphinx->escapeString($string);
    }

    /**
     * {@inheritdoc}
     */
    public function setMatchMode($mode)
    {
        $this->sphinx->setMatchMode($mode);
    }

    /**
     * {@inheritdoc}
     */
    public function setLimits($offset, $limit, $max = 0, $cutoff = 0)
    {
        $this->sphinx->setLimits($offset, $limit, $max, $cutoff);
    }

    /**
     * {@inheritdoc}
     */
    public function setFieldWeights(array $weights)
    {
        $this->sphinx->setFieldWeights($weights);
    }

    /**
     * {@inheritdoc}
     */
    public function setFilter($attribute, $values, $exclude = false)
    {
        $this->sphinx->setFilter($attribute, $values, $exclude);
    }

    /**
     * {@inheritdoc}
     */
    public function resetFilters()
    {
        $this->sphinx->resetFilters();
    }

    /**
     * {@inheritdoc}
     */
    public function addQuery(
        $query,
        array $indexes
    ) {
        $indexNames = '';
        foreach ($indexes as &$label) {
            if (isset($this->indexes[$label])) {
                $indexNames .= $this->indexes[$label] . ' ';
            }
        }

        if (!empty($indexNames)) {
            $this->sphinx->addQuery($query, $indexNames);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function runQueries()
    {
        return $this->sphinx->runQueries();
    }

}
