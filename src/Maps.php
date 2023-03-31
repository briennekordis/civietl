<?php
namespace Civietl;

class Maps {
  const PREFIX_MAP = [
    'Br.' => 'Brother',
    'Bro.' => 'Brother',
    'Doctor' => 'Dr.',
    'DR' => 'Dr.',
    'Dr' => 'Dr.',
    'dr' => 'Dr.',
    'DR.' => 'Dr.',
    'Dr. & Mrs.' => 'Dr. and Mrs.',
    'Dr./' => 'Dr.',
    'Dr./Ms' => 'Dr. and Mrs.',
    'Father' => 'Fr.',
    'Father' => 'Fr.',
    'Fr' => 'Fr.',
    'Hon.' => 'The Honorable',
    'Honorable' => 'The Honorable',
    'Honorable Mr.' => 'The Honorable',
    'Judge' => 'The Honorable',
    'miss' => 'Miss',
    'Miss.' => 'Miss',
    'Mister' => 'Mr.',
    'MR' => 'Mr.',
    'MR' => 'Mr.',
    'Mr' => 'Mr.',
    'Mr' => 'Mr.',
    'mr' => 'Mr.',
    'Mr & MRS' => 'Mr. and Mrs.',
    'Mr & Mrs' => 'Mr. and Mrs.',
    'Mr & Mrs.' => 'Mr. and Mrs.',
    'Mr and Mrs' => 'Mr. and Mrs.',
    'Mr,' => 'Mr.',
    'mr,' => 'Mr.',
    'MR.' => 'Mr.',
    'mr.' => 'Mr.',
    'Mr. & Mrs.' => 'Mr. and Mrs.',
    'Mr. & Ms.' => 'Mr. and Ms.',
    'Mr. and Ms.' => 'Mr. and Ms.',
    'Mr. Dean' => 'Dean',
    'Mr. Mr.' => 'Mr.',
    'Mr. The Hon.' => 'The Honorable',
    'Mrs' => 'Mrs.',
    'mrs' => 'Mrs.',
    'MRS' => 'Mrs.',
    'Mrs,' => 'Mrs.',
    'MRS.' => 'Mrs.',
    'mrs.' => 'Mrs.',
    'Mrss' => 'Mrs.',
    'MS' => 'Ms.',
    'Ms' => 'Ms.',
    'Ms' => 'Ms.',
    'ms' => 'Ms.',
    'Ms,' => 'Ms.',
    'MS.' => 'Ms.',
    'ms.' => 'Ms.',
    'Ms. Ms.' => 'Ms.',
    'Msr.' => 'Mrs.',
    'Prof' => 'Professor',
    'Prof' => 'Professor',
    'prof' => 'Professor',
    'Prof.' => 'Professor',
    'Prof. Ms.' => 'Professor',
    'professor' => 'Professor',
    'Professor (ret)' => 'Professor',
    'Rep' => 'Representative',
    'REV' => 'Rev.',
    'Rev' => 'Rev.',
    'rev' => 'Rev.',
    'Rev Dr' => 'Rev. Dr.',
    'Rev. Ms.' => 'Rev.',
    'Rev.Dr.' => 'Rev. Dr.',
    'Reverend' => 'Rev.',
    'Reverend' => 'Rev.',
    'reverend' => 'Rev.',
    'Sen' => 'Senator',
    'Sen.' => 'Senator',
    'SR.' => 'Sister',
    'Sr.' => 'Sister',
    'The Hon.' => 'The Honorable',
    'The Honorable Judge' => 'The Honorable',
  ];

  const SUFFIX_MAP = [
    'D.D.S.' => 'DDS',
    'Jr' => 'Jr.',
    'Ph. D.' => 'Ph.D.',
    'Ph.D' => 'Ph.D.',
    'Ph.D. J.D.' => 'Ph.D.',
    'PhD' => 'Ph.D.',
    'Phd.' => 'Ph.D.',
    'JD' => 'J.D.',
    'Esq' => 'Esq.',
    'JR' => 'Jr.',
    'Sr' => 'Sr.',
    'jr' => 'Jr.',
    ', Esq.' => 'Esq.',
    ', M.D.' => 'MD',
    ', Ph.D.' => 'Ph.D.',
    ', Sr.' => 'Sr.',
    'Atty' => 'Esq.',
    'C P A' => 'CPA',
    'C.P.A.' => 'CPA',
    'D D S' => 'DDS',
    'D O' => 'DO',
    'D. O.' => 'DO',
    'ESQ' => 'Esq.',
    'ESQUIRE' => 'Esq.',
    'Esq. PC' => 'Esq.',
    'M D' => 'MD',
    'M.D.' => 'MD',
    'PH. D.' => 'Ph.D.',
    'PH.D.' => 'Ph.D.',
    'Ph D' => 'Ph.D.',
    'Ph D.' => 'Ph.D.',
    'Ph. D' => 'Ph.D.',
    'Ph.D.' => 'Ph.D.',
    'R.N.' => 'RN',
    ', D.D.S.' => 'DDS',
  ];

  const COUNTRY_MAP = [
    'ALberta' => 'Canada',
    'Amsterdam' => 'Netherlands',
    'Armed Forces Pacific' => 'United States',
    'AUT' => 'Australia',
    'BC' => 'Canada',
    'Brasil' => 'Brazil',
    'CAN' => 'Canada',
    'CAN.' => 'Canada',
    'Columbia' => 'Colombia',
    'Congo' => 'Congo, The Democratic Republic of the',
    'D.F. Mexico' => 'Mexico',
    'DENMARK?' => 'Denmark',
    'ENGLAND' => 'United Kingdom',
    'England' => 'United Kingdom',
    'FRANCE?' => 'France',
    'GERMANY?' => 'Germany',
    'GREAT BRITAIN' => 'United Kingdom',
    'HOLLAND' => 'Netherlands',
    'IND' => 'India',
    'Iran' => 'Iran, Islamic Republic Of',
    'KOREA' => 'Korea, Republic of',
    'Korea' => 'Korea, Republic of',
    'Korea, North' => 'Korea, Democratic People\'s Republic of',
    'Korea, South' => 'Korea, Republic of',
    'Laos' => 'Lao People\'s Democratic Republic',
    'Macedonia' => 'Macedonia, Republic Of',
    'Mexico D.F.' => 'Mexico',
    'Mexico DF' => 'Mexico',
    'Mexico, D.F.' => 'Mexico',
    'MEXICO?' => 'Mexico',
    'Moçambique' => 'Mozambique',
    'Morroco' => 'Morocco',
    'No Address' => '',
    'NO SIGNATURE REQUIRED' => '',
    'Northern Ireland' => 'United Kingdom',
    'ON' => 'Canada',
    'ONT' => 'Canada',
    'ONT CAN' => 'Canada',
    'Ont, Canada' => 'Canada',
    'Ontario' => 'Canada',
    'Ontario Canada' => 'Canada',
    'Ontario,Can.' => 'Canada',
    'Other' => '',
    'P.R. CHINA' => 'China',
    'P.R.C.' => 'China',
    'POL' => 'Poland',
    'PR' => 'United States',
    'Puerto Rico' => 'United States',
    'Quebec' => 'Canada',
    'Republic of China' => 'China',
    'REPUBLIC OF KOREA' => 'Korea, Republic of',
    'RUSSIA' => 'Russian Federation',
    'Russia' => 'Russian Federation',
    'Sarajevo' => 'Bosnia and Herzegovina',
    'Scoti' => 'Canada',
    'SCOTLAND' => 'United Kingdom',
    'Scotland' => 'United Kingdom',
    'SENEGAL' => 'Senegal',
    'South Korea' => 'Korea, Republic of',
    'St. Martin' => 'Sint Maarten (Dutch Part)',
    'Syria' => 'Syrian Arab Republic',
    'The Netherlands' => 'Netherlands',
    'Toronto, Can.' => 'Canada',
    'U.A.E. (United Arab Emirates)' => 'United Arab Emirates',
    'U.K.' => 'United Kingdom',
    'U.S.A.' => 'United States',
    'UK' => 'United Kingdom',
    'United States of America' => 'United States',
    'United States of America' => 'United States',
    'USA' => 'United States',
    'Vietnam' => 'Viet Nam',
    'WALES' => 'United Kingdom',
    'Wales, UK' => 'United Kingdom',
    'West Germany' => 'Germany',
    'West Indies' => '',
    'Swizerland' => 'Switzerland',
    'SERBIA-MONTENEGRO' => 'Serbia and Montenegro',
    'Kosova' => 'Kosovo',
    'Nairobi' => 'Kenya',
    'Maroc' => 'Morocco',
    'Bucharest' => 'Romania',
    'Great Britain' => 'United Kingdom',
    'The NETHERLANDS' => 'Netherlands',
    'Republic of Ireland' => 'Ireland',
    'Suisse' => 'Switzerland',
    'Burma' => 'Myanmar',
    'Begium' => 'Belgium',
    'Austrailia' => 'Australia',
    'Irland' => 'Ireland',
    'Perú' => 'Peru',
    'Tanzania' => 'Tanzania, United Republic of',
    'Palestine' => 'Palestine, State of',
    'Russia' => 'Russian Federation',
    'zhongguo' => 'China',
    'N/A' => '',
    'unknown' => '',
    'Country' => '',
  ];

  const STATE_MAP = [
    'Select a state' => '',
    'Select State' => '',
    'State' => '',
    'Other' => '',
    'ZZ' => '',
    'N/A' => '',
    'unknown' => '',
    'KPK' => 'Khyber Pakhtun Khawa',
    'D.C.' => 'DC',
    'P.R.' => 'PR',
    'P.R' => 'PR',
    'V.I.' => 'VI',
    'Ontario MM5S 2C5' => 'Ontario',
    'ONT' => 'Ontario',
    'Saskatchewen' => 'Saskatchewan',
    'NY 10011' => 'NY',
    'Mass' => 'MA',
    'OT' => 'Ontario',
    'Zurich' => '',
    'ILL.' => 'IL',
    'B.C.' => 'BC',
    'UK' => '',
    'Alb' => 'Alberta',
    'PX' => '',
    'XX' => '',
    'Fl.' => 'FL',
    'tn.' => 'TN',
    'N.nc' => 'NC',
    'MD MD' => 'MD',
    'Guangdong Province' => 'Guangdong',
    'MA.' => 'MA',
    'D.C.' => 'DC',
    'ot' => '',
    'N.Y' => 'NY',
    'MAS' => 'MA',
    'NEV' => 'NV',
    'PEN' => 'PA',
    'ILL' => 'IL',
    'N.J' => 'NJ',
    'OHI' => 'OH',
    'COL' => 'CO',
    'MIC' => 'MI',
    'KEN' => 'KY',
    'FLO' => 'FL',
    'ON,' => 'ON',
    'MAR' => 'MD',
    'MIN' => 'MN',
    'CON' => 'CT',
    'Ont' => 'ON',
    'WAS' => 'WA',
    'GEO' => 'GA',
    'PA.' => 'PA',
    'DIS' => 'DC',
    'MN.' => 'MN',
    'VIR' => 'VA',
    'CAL' => 'CA',
    'LOU' => 'LA',
    'UTA' => 'UT',
    'ARI' => 'AZ',
    'ORE' => 'OR',
    'CT.' => 'CT',
    'RHO' => 'RI',
    'WIS' => 'WI',
    'IDA' => 'ID',
    'IND' => 'IN',
    'ALA' => 'AL',
    'ALB' => 'Alberta',
    'BC,' => 'BC',
    'DEL' => 'DE',
    'FL.' => 'FL',
    'GA.' => 'GA',
    'HAW' => 'HI',
    'IOW' => 'IA',
    'KAN' => 'KS',
    'MAI' => 'ME',
    'MD.' => 'MD',
    'MI.' => 'MI',
    'MIS' => 'MS',
    'MON' => 'MT',
    'N:Y' => 'NY',
    'N.C' => 'NC',
    'NEB' => 'Nebraska',
    'OKL' => 'OK',
    'QUE' => 'Quebec',
    'Que' => 'Quebec',
    'R.I' => 'RI',
    'S.C' => 'SC',
    'TEN' => 'TN',
    'Washington, DC' => 'DC',
  ];

}
