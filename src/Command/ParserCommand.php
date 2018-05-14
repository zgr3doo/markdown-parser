<?php


namespace MarkdownParser\Command;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class ParserCommand extends ContainerAwareCommand
{
    /**
     * @var  array
     */
    private $filename;

    /**
     * @var  string
     */
    private $projectDir;

    public function __construct($name = null)
    {
        parent::__construct($name);
    }

    public function configure()
    {
        $this->setName('markdown-parser')
            ->setDescription('Convert markdown input into HTML output')
            ->addArgument(
                'filename',
                InputArgument::REQUIRED,
                'Filename of the source of markdown data'
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->projectDir = $this->getContainer()->get('kernel')->getProjectDir();
        $this->logger = $this->setupLogger();
        $this->setFilename($input->getArgument('filename'));
        $markdownContent = $this->loadFileContent();
        $htmlContent = $this->parseMarkdownContent($markdownContent);
        $this->saveHtmlContent($htmlContent);
    }

    private function setupLogger()
    {
        $logger = $this->getContainer()->get('logger');
        $logger->pushHandler(
            new StreamHandler(
                sprintf('%s/log/parser.log', $this->projectDir),
                Logger::INFO
            )
        );
        return $logger;
    }

    /**
     * @return bool|string
     * @internal param string $filename
     */
    private function loadFileContent()
    {
        $markdownContents = '';
        $finder = $this->getContainer()->get('finder');

        $files = $finder->in($this->projectDir . '/input')->files()->name('*.md')->name($this->filename['basename']);

        foreach ($files as $current) {
            $markdownContents = $current->getContents();
            break;
        }

        if (empty($markdownContents)) {
            $this->logger->error('Error - markdown file not found');
        } else {
            $this->logger->info('Markdown file loaded');
        }

        return $markdownContents;
    }

    /**
     * @param string $markdownContent
     * @return string
     */
    private function parseMarkdownContent($markdownContent)
    {
        $parsedown = $this->getContainer()->get('parsedown');
        $htmlContent = $parsedown->text($markdownContent);
        return sprintf("<html lang=\"eng\"><body>\n%s\n</body></html>", $htmlContent);
    }

    private function saveHtmlContent($htmlContent)
    {
        $fileSaved = file_put_contents(
            sprintf('%s/output/%s.html', $this->projectDir,  $this->filename['filename']),
            $htmlContent);

        if (!$fileSaved) {
            $this->logger->error('Error - HTML file could not be saved');
        } else {
            $this->logger->info('HTML file saved');
        }
    }

    private function setFilename($filename)
    {
        $this->filename = pathinfo($filename);
    }
}