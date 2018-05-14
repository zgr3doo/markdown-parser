<?php

namespace MarkdownParser\Command;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ParserCommandTest extends KernelTestCase
{

    private $current_kernel;

    private $application;

    private $projectDir;

    public function setUp()
    {
        $this->current_kernel = self::bootKernel();
        $this->application = new Application($this->current_kernel);
        $this->application->add(new ParserCommand());
    }

    public function tearDown()
    {
        unlink(sprintf('%s/output/sample.html', $this->projectDir));
    }

    public function testExecute()
    {
        // given
        $command = $this->application->find('markdown-parser');
        $commandTester = new CommandTester($command);

        // when
        $commandTester->execute(array(
            'command'  => $command->getName(),
            'filename' => 'sample.md',
        ));

        // then
        $this->projectDir = $this->current_kernel->getProjectDir();
        $this->assertEquals(
            file_get_contents(sprintf('%s/output/expected_sample.html', $this->projectDir)),
            file_get_contents(sprintf('%s/output/sample.html', $this->projectDir))
        );
    }
}