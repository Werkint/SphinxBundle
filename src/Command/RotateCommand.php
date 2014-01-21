<?php
namespace Werkint\Bundle\SphinxBundle\Command;

use Diplom\Data\Entity\Category;
use Diplom\Data\Entity\Work;
use Doctrine\ORM\Internal\Hydration\IterableResult;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * RotateCommand.
 *
 * @author Bogdan Yurov <bogdan@yurov.me>
 */
class RotateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('werkint:sphinx:rotate')
            ->setDescription('Rotate sphinx indexes');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return null
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $output->write('Rotating indexes... ');
        $this->serviceIndexer()->rotateAll();
        $output->writeln('done');
    }

    // -- Services ---------------------------------------

    protected function serviceIndexer()
    {
        return $this->getContainer()->get('werkint.sphinx.indexer');
    }
}
