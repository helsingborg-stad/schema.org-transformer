<?php

namespace SchemaTransformer\Storage\TypesenseStorage;

enum TypesenseCollection: string
{
    case Event             = 'Event';
    case ExhibitionEvent   = 'ExhibitionEvent';
    case JobPostingPublic  = 'JobPosting.public';
    case JobPostingPrivate = 'JobPosting.private';
    case ElementarySchool  = 'ElementarySchool';
    case PreSchool         = 'PreSchool';
    case Project           = 'Project';
}
