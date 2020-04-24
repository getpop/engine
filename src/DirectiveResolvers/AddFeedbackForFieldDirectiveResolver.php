<?php

declare(strict_types=1);

namespace PoP\Engine\DirectiveResolvers;

use PoP\ComponentModel\Feedback\Tokens;
use PoP\ComponentModel\Schema\SchemaHelpers;
use PoP\ComponentModel\Schema\SchemaDefinition;
use PoP\ComponentModel\Directives\DirectiveTypes;
use PoP\Translation\Facades\TranslationAPIFacade;
use PoP\ComponentModel\TypeResolvers\TypeResolverInterface;
use PoP\ComponentModel\DirectiveResolvers\AbstractGlobalDirectiveResolver;

class AddFeedbackForFieldDirectiveResolver extends AbstractGlobalDirectiveResolver
{
    const DIRECTIVE_NAME = 'addFeedbackForField';
    public static function getDirectiveName(): string
    {
        return self::DIRECTIVE_NAME;
    }

    /**
     * This is a system directive
     *
     * @return string
     */
    public function getDirectiveType(): string
    {
        return DirectiveTypes::SYSTEM;
    }

    /**
     * Execute always, even if validation is false
     *
     * @return void
     */
    public function needsIDsDataFieldsToExecute(): bool
    {
        return false;
    }

    /**
     * Execute the directive
     *
     * @param TypeResolverInterface $typeResolver
     * @param array $idsDataFields
     * @param array $succeedingPipelineIDsDataFields
     * @param array $succeedingPipelineDirectiveResolverInstances
     * @param array $resultIDItems
     * @param array $unionDBKeyIDs
     * @param array $dbItems
     * @param array $previousDBItems
     * @param array $variables
     * @param array $messages
     * @param array $dbErrors
     * @param array $dbWarnings
     * @param array $dbDeprecations
     * @param array $schemaErrors
     * @param array $schemaWarnings
     * @param array $schemaDeprecations
     * @return void
     */
    public function resolveDirective(
        TypeResolverInterface $typeResolver,
        array &$idsDataFields,
        array &$succeedingPipelineIDsDataFields,
        array &$succeedingPipelineDirectiveResolverInstances,
        array &$resultIDItems,
        array &$unionDBKeyIDs,
        array &$dbItems,
        array &$previousDBItems,
        array &$variables,
        array &$messages,
        array &$dbErrors,
        array &$dbWarnings,
        array &$dbDeprecations,
        array &$schemaErrors,
        array &$schemaWarnings,
        array &$schemaDeprecations
    ) {
        $type = $this->directiveArgsForSchema['type'] ?? $this->getDefaultFeedbackType();
        $target = $this->directiveArgsForSchema['target'] ?? $this->getDefaultFeedbackTarget();
        if ($target == 'db') {
            $translationAPI = TranslationAPIFacade::getInstance();
            foreach ($idsDataFields as $id => $dataFields) {
                // Use either the default value passed under param "value" or, if this is NULL, use a predefined value
                $expressions = $this->getExpressionsForResultItem($id, $variables, $messages);
                $resultItem = $resultIDItems[$id];
                list(
                    $resultItemValidDirective,
                    $resultItemDirectiveName,
                    $resultItemDirectiveArgs
                ) = $this->dissectAndValidateDirectiveForResultItem($typeResolver, $resultItem, $variables, $expressions, $dbErrors, $dbWarnings, $dbDeprecations);
                // Check that the directive is valid. If it is not, $dbErrors will have the error already added
                if (is_null($resultItemValidDirective)) {
                    continue;
                }
                // Take the default value from the directiveArgs
                $message = $resultItemDirectiveArgs['message'];
                // Check that the message was composed properly (eg: it didn't fail).
                // If it is not, $dbErrors will have the error already added
                if (is_null($message)) {
                    $dbErrors[(string)$id][] = [
                        Tokens::PATH => [$this->directive],
                        Tokens::MESSAGE => $translationAPI->__(
                            'The message could not be composed. Check previous errors',
                            'engine'
                        ),
                    ];
                    continue;
                }
                $feedbackMessageEntry = $this->getFeedbackMessageEntry($message);
                if ($type == 'warning') {
                    $dbWarnings[(string)$id][] = $feedbackMessageEntry;
                } elseif ($type == 'deprecation') {
                    $dbDeprecations[(string)$id][] = $feedbackMessageEntry;
                }
            }
        } elseif ($target == 'schema') {
            $message = $this->directiveArgsForSchema['message'];
            $feedbackMessageEntry = $this->getFeedbackMessageEntry($message);
            if ($type == 'warning') {
                $schemaWarnings[] = $feedbackMessageEntry;
            } elseif ($type == 'deprecation') {
                $schemaDeprecations[] = $feedbackMessageEntry;
            }
        }
    }

    protected function getFeedbackMessageEntry(string $message): array
    {
        return [
            Tokens::PATH => [$this->directive],
            Tokens::MESSAGE => $message,
        ];
    }

    public function getSchemaDirectiveDescription(TypeResolverInterface $typeResolver): ?string
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        return $translationAPI->__('Whenever a field is queried, add a feedback message to the response, of either type "warning", "deprecation" or "log"', 'engine');
    }

    public function getSchemaDirectiveArgs(TypeResolverInterface $typeResolver): array
    {
        $translationAPI = TranslationAPIFacade::getInstance();
        return [
            [
                SchemaDefinition::ARGNAME_NAME => 'message',
                SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_STRING,
                SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The feedback message', 'engine'),
                SchemaDefinition::ARGNAME_MANDATORY => true,
            ],
            [
                SchemaDefinition::ARGNAME_NAME => 'type',
                SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_ENUM,
                SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The type of feedback', 'engine'),
                SchemaDefinition::ARGNAME_MANDATORY => true,
                SchemaDefinition::ARGNAME_ENUMVALUES => SchemaHelpers::convertToSchemaFieldArgEnumValueDefinitions(
                    $this->getFeedbackTypes()
                ),
                SchemaDefinition::ARGNAME_DEFAULT_VALUE => $this->getDefaultFeedbackType(),
            ],
            [
                SchemaDefinition::ARGNAME_NAME => 'target',
                SchemaDefinition::ARGNAME_TYPE => SchemaDefinition::TYPE_ENUM,
                SchemaDefinition::ARGNAME_DESCRIPTION => $translationAPI->__('The target for the feedback', 'engine'),
                SchemaDefinition::ARGNAME_MANDATORY => true,
                SchemaDefinition::ARGNAME_ENUMVALUES => SchemaHelpers::convertToSchemaFieldArgEnumValueDefinitions(
                    $this->getFeedbackTargets()
                ),
                SchemaDefinition::ARGNAME_DEFAULT_VALUE => $this->getDefaultFeedbackTarget(),
            ],
        ];
    }

    protected function getFeedbackTypes(): array
    {
        return [
            'warning',
            'deprecation',
        ];
    }

    protected function getDefaultFeedbackType(): string
    {
        return 'deprecation';
    }

    protected function getFeedbackTargets(): array
    {
        return [
            'db',
            'schema',
        ];
    }

    protected function getDefaultFeedbackTarget(): string
    {
        return 'schema';
    }
}
