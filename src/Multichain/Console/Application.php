<?php
namespace Multichain\Console;

use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{

    const VERSION = '@package_version@';
    const RELEASE_DATE = '@release_date@';
    const LOGO = '<fg=green;options=bold>
  __  __       _ _   _      _           _          ____ _     ___
 |  \/  |_   _| | |_(_) ___| |__   __ _(_)_ __    / ___| |   |_ _|
 | |\/| | | | | | __| |/ __| \'_ \ / _` | | \'_ \  | |   | |    | |
 | |  | | |_| | | |_| | (__| | | | (_| | | | | | | |___| |___ | |
 |_|  |_|\__,_|_|\__|_|\___|_| |_|\__,_|_|_| |_|  \____|_____|___|
</fg=green;options=bold>
';

    public function __construct()
    {
        parent::__construct('Multichain CLI', Application::VERSION);
        $this->add(new Command\CreateAddressCommand());
        $this->add(new Command\IssueAssetCommand());
        $this->add(new Command\ListAssetsCommand());
        $this->add(new Command\ListAddressesCommand());
        $this->add(new Command\SendCommand());
        $this->add(new Command\ListTransactionsCommand());
        $this->add(new Command\ExchangeCommand());
        $this->add(new Command\RegisterDocumentCommand());
        $this->add(new Command\ValidateDocumentCommand());
    }

    public function getHelp()
    {
        return Application::LOGO . parent::getHelp();
    }
}