<?php

namespace CoreBundle\Command;

use CoreBundle\Entity\Enterprise;
use CoreBundle\Entity\Plan;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCsvClientCommand extends ContainerAwareCommand
{
    const ENTERPRISE    = 0;
    const CONTACT       = 1;
    const TIME_LEFT     = 2;
    const EXPIRE_AT     = 3;
    const DESCRIPTION   = 4;

    protected function configure()
    {
        $this
            ->setName('import:csv:client')
            ->setDescription('Import list of clients')
            ->addArgument('file', InputArgument::REQUIRED, 'csv file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');

        /** @var \CoreBundle\Service\ParseCSV $parser */
        $parser = $this->getContainer()->get('core.parser_csv');
        $rows = $parser->parse($file);

        foreach ($rows as $row) {
            $enterprise = $this->getEnterpriseByName($row[self::ENTERPRISE], $row[self::CONTACT]);
            $plan = $this->createPlan($row[self::TIME_LEFT], $row[self::EXPIRE_AT], $row[self::DESCRIPTION]);

            $plan->setEnterprise($enterprise);
        }

        $this->getManager()->flush();

        $output->writeln('Command result.');
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    private function getManager()
    {
        $doctrine = $this->getContainer()->get('doctrine');

        return $doctrine->getManager();
    }

    /**
     * Return an Entreprise entity by name
     *
     * @param $name
     * @param $contact
     *
     * @return Enterprise
     */
    private function getEnterpriseByName($name, $contact)
    {
        $em = $this->getManager();

        /** @var \Doctrine\Common\Persistence\ObjectRepository $repo */
        $repo = $em->getRepository('CoreBundle:Enterprise');

        $enterprise = $repo->findOneBy(['name' => $name]);

        if (is_null($enterprise)) {
            $enterprise = new Enterprise();
            $enterprise->setName($name)
                        ->setContact($contact);

            $em->persist($enterprise);
            $em->flush();
        }

        return $enterprise;
    }

    /**
     * Create a Plan entity
     *
     * @param $timeLeft
     * @param $expireAt
     * @param $description
     *
     * @return Plan
     */
    private function createPlan($timeLeft, $expireAt, $description)
    {
        $description = (empty($description)) ? "import initial" : $description;

        $plan = new Plan();
        $plan->setDescription($description)
             ->setExpireAt($this->getDateTime($expireAt))
             ->setTime($this->makeTime($timeLeft));

        $this->getManager()->persist($plan);

        return $plan;
    }

    /**
     * @param $datetime
     * @return \DateTime
     */
    private function getDateTime($datetime)
    {
        return (empty($datetime)) ? new \DateTime('now +1 year') : \DateTime::createFromFormat("d/m/Y", $datetime);
    }

    private function makeTime($time)
    {
        $time = explode('h', $time);

        $seconds = $time[0] * 3600 + $time[1] * 60;

        return new \DateTime('1st january 1970 ' . $seconds . ' seconds');
    }
}
