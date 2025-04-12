<?php

declare(strict_types=1);

/*
 * This file is part of the package stefanfroemken/ext-kickstarter.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace StefanFroemken\ExtKickstarter\Creator\Property\TypeConverter;

use StefanFroemken\ExtKickstarter\Information\TypeConverterInformation;
use Symfony\Component\Yaml\Yaml;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\Exception\MissingArrayPathException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RegisterTypeConverterCreator implements TypeConverterCreatorInterface
{
    public function create(TypeConverterInformation $typeConverterInformation): void
    {
        $configurationPath = $typeConverterInformation->getExtensionInformation()->getExtensionPath() . 'Configuration/';
        GeneralUtility::mkdir_deep($configurationPath);

        $servicesYamlPath = $configurationPath . 'Services.yaml';

        $servicesYamlData = Yaml::parseFile($servicesYamlPath);
        if (!$this->hasRegisteredTypeConverter($servicesYamlData, $typeConverterInformation)) {
            $servicesYamlData = $this->addTypeConverterRegistration($servicesYamlData, $typeConverterInformation);
        }

        file_put_contents(
            $servicesYamlPath,
            Yaml::dump(
                $servicesYamlData,
                99,
                2,
                Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE | Yaml::DUMP_OBJECT_AS_MAP
            )
        );
    }

    private function addTypeConverterRegistration(array $servicesYamlData, TypeConverterInformation $typeConverterInformation): array
    {
        try {
            return ArrayUtility::setValueByPath(
                $servicesYamlData,
                'services/' . $this->getTypeConverterClassname($typeConverterInformation),
                [
                    'tags' => [
                        0 => [
                            'name' => 'extbase.type_converter',
                            'priority' => $typeConverterInformation->getPriority(),
                            'sources' => $typeConverterInformation->getSource(),
                            'target' => $typeConverterInformation->getTarget(),
                        ],
                    ],
                ],
            );
        } catch (\RuntimeException) {
        }

        return $servicesYamlData;
    }

    private function hasRegisteredTypeConverter(array $servicesYamlData, TypeConverterInformation $typeConverterInformation): bool
    {
        try {
            $configuration = ArrayUtility::getValueByPath(
                $servicesYamlData,
                'services/' . $this->getTypeConverterClassname($typeConverterInformation),
            );
            return is_array($configuration);
        } catch (\RuntimeException | MissingArrayPathException) {
        }

        return false;
    }

    private function getTypeConverterClassname(TypeConverterInformation $typeConverterInformation): string
    {
        return sprintf(
            '%s\\%s',
            $typeConverterInformation->getNamespace(),
            $typeConverterInformation->getTypeConverterClassName(),
        );
    }
}
