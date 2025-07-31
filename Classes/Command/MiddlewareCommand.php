<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Command;

use Psr\Container\ContainerInterface;
use StefanFroemken\ExtKickstarter\Information\MiddleWareInformation;
use StefanFroemken\ExtKickstarter\Service\Creator\MiddlewareCreatorService;
use StefanFroemken\ExtKickstarter\Traits\AskForExtensionKeyTrait;
use StefanFroemken\ExtKickstarter\Traits\CreatorInformationTrait;
use StefanFroemken\ExtKickstarter\Traits\ExtensionInformationTrait;
use StefanFroemken\ExtKickstarter\Traits\TryToCorrectClassNameTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MiddlewareCommand extends Command
{
    use AskForExtensionKeyTrait;
    use CreatorInformationTrait;
    use ExtensionInformationTrait;
    use TryToCorrectClassNameTrait;

    public function __construct(
        private readonly MiddlewareCreatorService $middlewareCreatorService,
        protected readonly ContainerInterface $container,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'extension_key',
            InputArgument::OPTIONAL,
            'Provide the extension key you want to extend.',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Welcome to the TYPO3 Extension Builder');

        $io->text([
            'We are here to assist you in creating a new TYPO3 Middleware.',
            'Now, we will ask you a few questions to customize the middleware according to your needs.',
            'Please take your time to answer them.',
        ]);

        $middlewareInformation = $this->askForMiddlewareInformation($io, $input);
        $this->middlewareCreatorService->create($middlewareInformation);
        $this->printCreatorInformation($middlewareInformation->getCreatorInformation(), $io);

        return Command::SUCCESS;
    }

    private function askForMiddlewareInformation(SymfonyStyle $io, InputInterface $input): MiddlewareInformation
    {
        $extensionInformation = $this->getExtensionInformation(
            $this->askForExtensionKey($io, $input->getArgument('extension_key')),
            $io
        );

        return new MiddlewareInformation(
            $extensionInformation,
            $this->askForClassName($io),
            $stack = $this->askForMiddlewareStack($io),
            $this->askForMiddlewareIdentifier($io, $extensionInformation->getComposerPackageName() . '/', $stack),
            $this->askForBeforeAfter($io, $stack, 'before'),
            $this->askForBeforeAfter($io, $stack, 'after'),
        );
    }

    private function askForClassName(SymfonyStyle $io): string
    {
        $defaultMiddlewareClassName = null;

        do {
            $className = (string)$io->ask(
                'Please provide the class name of your new Middleware',
                $defaultMiddlewareClassName,
            );

            if (preg_match('/^\d/', $className)) {
                $io->error('Class name should not start with a number.');
                $defaultMiddlewareClassName = $this->tryToCorrectClassName($className, 'Middleware');
                $validClassName = false;
            } elseif (preg_match('/[^a-zA-Z0-9]/', $className)) {
                $io->error('Class name contains invalid chars. Please provide just letters and numbers.');
                $defaultMiddlewareClassName = $this->tryToCorrectClassName($className, 'Middleware');
                $validClassName = false;
            } elseif (preg_match('/^[A-Z][a-zA-Z0-9]+$/', $className) === 0) {
                $io->error('Class name must be written in UpperCamelCase like "StatusCheckMiddleware".');
                $defaultMiddlewareClassName = $this->tryToCorrectClassName($className, 'Middleware');
                $validClassName = false;
            } elseif (!str_ends_with($className, 'Middleware')) {
                $io->error('Class name must end with "Middleware".');
                $defaultMiddlewareClassName = $this->tryToCorrectClassName($className, 'Middleware');
                $validClassName = false;
            } else {
                $validClassName = true;
            }
        } while (!$validClassName);

        return $className;
    }

    private function askForMiddlewareIdentifier(SymfonyStyle $io, string $vendorPrefix, string $stack): string
    {
        $default = null;

        $configuration = $this->getConfiguration();

        do {
            $valid = false;
            $identifier = (string)$io->ask(
                'Please provide the middleware identifier (e.g., "' . $vendorPrefix . 'csp-headers")',
                $default
            );

            // Simple check for empty input
            if (trim($identifier) === '') {
                $io->warning('Middleware identifier cannot be empty.');
                continue;
            }

            // Ensure prefix
            if (!str_starts_with($identifier, $vendorPrefix)) {
                $io->warning(sprintf('Identifier does not start with "%s". It must be prefixed.', $vendorPrefix));
                $default = $vendorPrefix . ltrim($identifier, '/');
                continue;
            }

            // Extract the part after the prefix for validation
            $namePart = substr($identifier, strlen($vendorPrefix));

            // Validate only the name part
            if (preg_match('/^[a-z0-9]+(-[a-z0-9]+)*$/', $namePart) === 0) {
                $io->warning('The part after the prefix must be lowercase, may include numbers, and can use hyphens.');
                $corrected = $this->tryToCorrectIdentifier($namePart);
                $identifier = $vendorPrefix . $corrected;
                $default = $identifier;
                continue;
            }
            if (in_array($identifier, $configuration[$stack] ?? [], true)) {
                $io->warning(sprintf('The identifier "%s" already exists in the configuration!', $identifier));
                // you might still allow it, or force user to change:
                $default = $identifier;
                continue;
            }
            $valid = true;

        } while (!$valid);

        return $identifier;
    }

    private function tryToCorrectIdentifier(string $input): string
    {
        $corrected = strtolower($input);
        $corrected = preg_replace('/[^a-z0-9]+/', '-', $corrected);
        $corrected = trim($corrected, '-');

        if ($corrected === '') {
            return 'my-middleware';
        }

        return $corrected;
    }

    private function askForMiddlewareStack(SymfonyStyle $io): string
    {
        return $io->choice(
            'Is this middleware stack for the frontend or backend?',
            ['frontend', 'backend'],
            'frontend',
        );
    }

    /**
     * @return mixed[]
     */
    private function askForBeforeAfter(SymfonyStyle $io, string $stack, string $location): array
    {
        $entries  = array_keys($this->getConfiguration()[$stack] ?? []);

        array_unshift($entries, 'none');

        $answer = $io->choice(
            'Should this middleware be executed ' . $location . ' specific middleware(s)? (select "none" if not applicable)',
            $entries,
            'none',
            true
        );

        // Normalize the result
        if (in_array('none', $answer, true)) {
            return []; // user chose "none" -> treat as no dependencies
        }

        return $answer;
    }

    public function getConfiguration(): array
    {
        $configurationArray = [];
        foreach (['frontend', 'backend'] as $stackName) {
            // reversing the array allows the admin to read the stack from top to bottom
            $configurationArray[$stackName] = array_reverse((array)$this->container->get($stackName . '.middlewares'));
        }
        $configurationArray['raw'] = $this->container->get('middlewares');

        return $configurationArray;
    }
}
