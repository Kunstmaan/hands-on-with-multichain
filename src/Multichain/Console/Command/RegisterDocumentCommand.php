<?php


namespace Multichain\Console\Command;

use Multichain\Console\Entity\Document;
use Multichain\Console\Shared\InputOutput;
use Multichain\Console\Shared\MultichainCommands;
use Multichain\Console\Shared\MysqlCommands;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RegisterPlotCommand
 * @package Multichain\Console\Command
 */
class RegisterDocumentCommand extends Command
{
    use MultichainCommands;
    use MysqlCommands;
    use InputOutput;

    protected function configure()
    {
        $this
            ->setName('register-document')
            ->addArgument("document", InputArgument::REQUIRED, "The path to the document")
            ->setDescription('Register a document in our database and saves the has to the blockchain')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->prepareExecute($input, $output, $this);
        $this->connectMultichain();
        $this->multichainDebug($output);
        $this->connectMysql();

        $documentPath = $input->getArgument("document");

        $document = new Document();
        $document->setContent(file_get_contents($documentPath));
        $hash = hash('sha256', $document->getContent());
        $this->io->text("Calculating the sha256 hash <fg=blue>(" . $hash . ")</fg=blue> of the content of the file");
        $document->setHash($hash);
        $this->io->text("Store a record in the MySQL database with the content of <fg=blue>".$documentPath."</fg=blue>");
        $this->io->text("    and the content hash <fg=blue>".$document->getHash()."</fg=blue>");
        $this->em->persist($document);
        $this->em->flush();

        $this->io->text("Register a transaction that holds both the hash <fg=blue>(" . $hash . ")</fg=blue>");
        $this->io->text("    and the database record ID <fg=blue>(" . $document->getID() . ")</fg=blue> in hexadecimal format.");
        $opdata = array();
        $opdata['hash'] = $hash;
        $opdata['id'] = $document->getID();
        $documentRegistry = $this->getAddressByKyc("documentregistry")->getAddress();
        $hex = $this->multichain->sendWithMetadataFrom($documentRegistry, $documentRegistry, array("DOCUMENT" => 1), bin2hex(json_encode($opdata)));

        $document->setHex($hex);
        $this->em->persist($document);
        $this->em->flush();
    }
}
