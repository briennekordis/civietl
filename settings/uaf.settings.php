<?php
$clientName = 'uaf';
require_once "$clientName.settings.local.php";
$logFolder = "$workroot/errors";
$importDefaults = [
  'project_name' => 'Uaf',
  'reader_type' => 'CsvReader',
  'cache_type' => 'ArrayCache',
  'writer_type' => 'CiviCrmApi4',
];
$importSettings = [
  'StepOne' => [
    'readerOptions' => [
      // required for CsvReader: file_path, data_primary_key.
      'file_path' => "$workroot/raw data/full_archive/constituents.csv",
      'data_primary_key' => 'LGL Constituent ID',
    ],
    'writerOptions' => [
      // required for CsvWriter/JsonWriter: file_path.
      'file_path' => "$workroot/data/contacts.csv",
      // required for CiviCrmApi4: civi_primary_entity.
      'civi_primary_entity' => 'Contact',
    ],
  ],
  // 'StepTwo' => [
  //   'readerOptions' => [
  //     // required for CsvReader: file_path, data_primary_key.
  //     'file_path' => "$workroot/raw data/spouse_test.csv",
  //     'data_primary_key' => 'LGL Constituent ID',
  //   ],
  //   'writerOptions' => [
  //     // required for CsvWriter/JsonWriter: file_path.
  //     'file_path' => "$workroot/data/contacts2.csv",
  //     // required for CiviCrmApi4: civi_primary_entity.
  //     'civi_primary_entity' => 'Contact',
  //   ],
  // ],
  'ContactSubtypes' => [
    'readerOptions' => [
      'file_path' => "$workroot/raw data/contact_subtypes.csv",
      'data_primary_key' => 'Contact Type ID',
    ],
    'writerOptions' => [
      'civi_primary_entity' => 'ContactType',
      'file_path' => "$workroot/data/tempCT.csv",
    ],
  ],
  'Websites' => [
    'readerOptions' => [
      'file_path' => "$workroot/raw data/full_archive/websites.csv",
      'data_primary_key' => 'LGL Website ID',
    ],
    'writerOptions' => [
      'civi_primary_entity' => 'Website',
    ],
  ],
  'Emails' => [
    'readerOptions' => [
      'file_path' => "$workroot/raw data/full_archive/emails.csv",
      'data_primary_key' => 'LGL Email ID',
    ],
    'writerOptions' => [
      'civi_primary_entity' => 'Email',
    ],
  ],
  'Phones' => [
    'readerOptions' => [
      'file_path' => "$workroot/raw data/full_archive/phone_numbers.csv",
      'data_primary_key' => 'LGL Phone ID',
    ],
    'writerOptions' => [
      'civi_primary_entity' => 'Phone',
    ],
  ],
  'Addresses' => [
    'readerOptions' => [
      'file_path' => "$workroot/raw data/full_archive/street_addresses.csv",
      'data_primary_key' => 'LGL Address ID',
    ],
    'writerOptions' => [
      'civi_primary_entity' => 'Address',
      'file_path' => "$workroot/data/addresses.csv",
    ],
    'writer_type' => 'CsvWriter',
  ],
  'Notes' => [
    'readerOptions' => [
      'file_path' => "$workroot/raw data/full_archive/notes.csv",
      'data_primary_key' => 'LGL Note ID',
    ],
    'writerOptions' => [
      'civi_primary_entity' => 'Note',
    ],
  ],
  'Groups' => [
    'readerOptions' => [
      'file_path' => "$workroot/raw data/full_archive/constituents.csv",
      'data_primary_key' => 'LGL Constituent ID',
    ],
    'writerOptions' => [
      'civi_primary_entity' => 'GroupContact',
    ],
  ],
  'FinancialTypes' => [
    'readerOptions' => [
      'file_path' => "$workroot/raw data/full_archive/funds.csv",
      'data_primary_key' => 'LGL Fund ID',
    ],
    'writerOptions' => [
      'civi_primary_entity' => 'FinancialType',
      'file_path' => "$workroot/data/tempFT.csv",
    ],
  ],
  'OptionGroups' => [
    'readerOptions' => [
      'file_path' => "$workroot/raw data/optionGroups.csv",
      'data_primary_key' => 'Option Group ID',
    ],
    'writerOptions' => [
      'civi_primary_entity' => 'OptionGroup',
      'file_path' => "$workroot/data/tempOG.csv",
    ],
  ],
  'OptionValues' => [
    'readerOptions' => [
      'file_path' => "$workroot/raw data/optionValues.csv",
      'data_primary_key' => 'Option Values ID',
    ],
    'writerOptions' => [
      'civi_primary_entity' => 'OptionValue',
      'file_path' => "$workroot/data/tempOV.csv",
    ],
  ],
  'CustomGroups' => [
    'readerOptions' => [
      'file_path' => "$workroot/raw data/customGroups.csv",
      'data_primary_key' => 'Custom Group ID',
    ],
    'writerOptions' => [
      'civi_primary_entity' => 'CustomGroup',
      'file_path' => "$workroot/data/tempCG.csv",
      'match_fields' => ['name'],
    ],
  ],
  'CustomFields' => [
    'readerOptions' => [
      'file_path' => "$workroot/raw data/customFields.csv",
      'data_primary_key' => 'Custom Field ID',
    ],
    'writerOptions' => [
      'civi_primary_entity' => 'CustomField',
      'file_path' => "$workroot/data/tempCF.csv",
    ],
  ],
  'Campaigns' => [
    'readerOptions' => [
      'file_path' => "$workroot/raw data/full_archive/campaigns.csv",
      'data_primary_key' => 'LGL Campaign ID',
    ],
    'writerOptions' => [
      'civi_primary_entity' => 'Campaign',
      'file_path' => "$workroot/data/tempCmp.csv",
    ],
  ],
  'Appeals' => [
    'readerOptions' => [
      'file_path' => "$workroot/raw data/full_archive/appeals.csv",
      'data_primary_key' => 'LGL Appeal ID',
    ],
    'writerOptions' => [
      'civi_primary_entity' => 'Campaign',
      'file_path' => "$workroot/data/tempA.csv",
    ],
  ],
  'Contributions' => [
    'readerOptions' => [
      'file_path' => "$workroot/raw data/full_archive/gift_gifts.csv",
      'data_primary_key' => 'LGL Gift ID',
    ],
    'writerOptions' => [
      'civi_primary_entity' => 'Contribution',
      'file_path' => "$workroot/data/tempCon.csv",
    ],
  ],
  'ContributionNotes' => [
    'readerOptions' => [
      'file_path' => "$workroot/raw data/full_archive/gift_gifts.csv",
      'data_primary_key' => '',
    ],
    'writerOptions' => [
      'civi_primary_entity' => 'Note',
      'file_path' => "$workroot/data/tempConNotes.csv",
    ],
    'writer_type' => 'CsvWriter',
  ],
  'RelationshipTypes' => [
    'readerOptions' => [
      'file_path' => "$workroot/raw data/relationshipTypes.csv",
      'data_primary_key' => '',
    ],
    'writerOptions' => [
      'civi_primary_entity' => 'RelationshipType',
      // This allows me to update existing records by matching on these fields.
      'match_fields' => ['name_a_b', 'name_b_a'],
    ],
  ],
  'Relationships' => [
    'readerOptions' => [
      'file_path' => "$workroot/raw data/full_archive/constituent_relationships.csv",
      'data_primary_key' => '',
    ],
    'writerOptions' => [
      'civi_primary_entity' => 'Relationship',
    ],
  ],
];
