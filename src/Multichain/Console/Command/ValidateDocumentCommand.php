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

class ValidateDocumentCommand extends Command
{
    use MultichainCommands;
    use MysqlCommands;
    use InputOutput;

    protected function configure()
    {
        $this
            ->setName('validate-document')
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

        $content = file_get_contents($documentPath);

        $hash = hash('sha256', $content);

        /** @var Document $ormDocument */
        $ormDocument = $this->em->getRepository('Multichain\Console\Entity\Document')->findOneByContent($content);

        if ($ormDocument) {
            $hex = $ormDocument->getHex();
            $transaction = $this->multichain->getRawTransaction($hex, 1);
            $data = $transaction["data"][0];
            $parsedData = json_decode(hex2bin($data), true);
            $originalHash = $parsedData["hash"];

            $this->io->text("Submitted document has a hash of <fg=blue>" . $hash . "</fg=blue>");
            $this->io->text("Found document the same content in the MySQL database");
            if ($hash ==  $originalHash){
                $this->io->text("Validated the hash found in the database with the Blockchain");
                $this->io->success('Hashes match! ' . $documentPath . ' is validated by the Blockchain');
            } else {
                $this->io->text("Unable to validated the hash found in the database with the Blockchain");
                $this->io->error('Hashes do not match! ' . $documentPath . ' is NOT validated by the Blockchain');
            }
        } else {
            $this->io->text("Submitted document has a hash of <fg=blue>" . $hash . "</fg=blue>");
            $this->io->text("Found no document with the same content.");
            $this->io->error('Hashes do not match! ' . $documentPath . ' is NOT validated by the Blockchain');
        }
    }

}