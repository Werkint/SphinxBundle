<?php
namespace Werkint\Bundle\SphinxBundle\Services;

use Symfony\Component\Process\ProcessBuilder;

/**
 * Indexer.
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class Indexer
{
    /**
     * @var string $bin
     */
    protected $bin;

    /**
     * @var array $indexes
     */
    protected $indexes;

    /**
     * Constructor.
     *
     * @param string $bin The path to the indexer executable.
     * @param array $indexes The list of indexes that can be used.
     */
    public function __construct(
        $bin = '/usr/bin/indexer',
        array $indexes = []
    ) {
        $this->bin = $bin;
        $this->indexes = $indexes;
    }

    /**
     * Rebuild and rotate all indexes.
     */
    public function rotateAll()
    {
        $this->rotate(array_keys($this->indexes));
    }

    /**
     * Rebuild and rotate the specified index(es).
     *
     * @param array|string $indexes The index(es) to rotate.
     * @throws \RuntimeException
     */
    public function rotate($indexes)
    {
        $pb = new ProcessBuilder();
        $pb
            ->inheritEnvironmentVariables()
            ->add($this->bin)
            ->add('--rotate');
        if (is_array($indexes)) {
            foreach ($indexes as &$label) {
                if (isset($this->indexes[$label])) {
                    $pb->add($this->indexes[$label]);
                }
            }
        } elseif (is_string($indexes)) {
            if (isset($this->indexes[$indexes])) {
                $pb->add($this->indexes[$indexes]);
            }
        } else {
            throw new \RuntimeException(sprintf(
                'Indexes can only be an array or string, %s given.',
                gettype($indexes)
            ));
        }

        $indexer = $pb->getProcess();
        $code = $indexer->run();

        if (($errStart = strpos($indexer->getOutput(), 'FATAL:')) !== false) {
            if (($errEnd = strpos($indexer->getOutput(), "\n", $errStart)) !== false) {
                $errMsg = substr($indexer->getOutput(), $errStart, $errEnd);
            } else {
                $errMsg = substr($indexer->getOutput(), $errStart);
            }
            throw new \RuntimeException(sprintf(
                'Error rotating indexes: "%s".',
                rtrim($errMsg)
            ));
        }
    }

}
