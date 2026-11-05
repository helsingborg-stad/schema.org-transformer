<?php

declare(strict_types=1);

namespace SchemaTransformer\Transforms\JobPosting\Visma;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

#[CoversClass(VismaJobPostingTransform::class)]
final class VismaJobPostingTransformTest extends TestCase
{
    #[TestDox('maps a Visma list assignment and its item feed to a JobPosting')]
    public function testMapsAssignmentToJobPosting(): void
    {
        $transform = $this->transformWithItem(<<<'XML'
            <Assignment>
                <EmploymentStartDate>2024-06-01T00:00:00</EmploymentStartDate>
                <ReadMoreUrl>https://example.test/job</ReadMoreUrl>
                <ApplicationMethods>
                    <ApplicationMethod>
                        <ValueXml>
                            <web>
                                <url>https://recruit.visma.com/apply?id=1</url>
                            </web>
                        </ValueXml>
                    </ApplicationMethod>
                </ApplicationMethods>
                <Localization>
                    <AssignmentLoc>
                        <DepartmentDescr>Om oss</DepartmentDescr>
                        <WorkDescr>Arbetsuppgifter här</WorkDescr>
                        <Qualifications>Krav</Qualifications>
                        <AdditionalInfo>Se https://example.test/info</AdditionalInfo>
                        <EmploymentStartDateDescr>Enligt överenskommelse</EmploymentStartDateDescr>
                        <EmploymentGrade><Name>Heltid</Name></EmploymentGrade>
                        <EmploymentDuration><Descr></Descr><Name>Tillsvidare</Name></EmploymentDuration>
                        <OccupationClassifications>
                            <OccupationClassification LevelId="20">
                                <Level>1</Level>
                                <Name>Administration</Name>
                                <Descr>Yrkesbeskrivning från Visma</Descr>
                            </OccupationClassification>
                        </OccupationClassifications>
                        <ContactPersons>
                            <ContactPerson>
                                <Title>Chef</Title>
                                <ContactName>Ada Lovelace</ContactName>
                                <Email>ada@example.test</Email>
                                <Telephone>0701234567</Telephone>
                            </ContactPerson>
                        </ContactPersons>
                    </AssignmentLoc>
                </Localization>
            </Assignment>
            XML);

        $jobs = $transform->transform(['content' => $this->listXml()]);

        $this->assertCount(1, $jobs);
        $job = $jobs[0];
        $this->assertSame('C100', $job['@id']);
        $this->assertSame('JobPosting', $job['@type']);
        $this->assertSame('2', $job['totalJobOpenings']);
        $this->assertSame('Lärare', $job['title']);
        $this->assertSame('2024-06-01 – Enligt överenskommelse', $job['jobStartDate']);
        $this->assertSame('Lista arbete', $job['responsibilities']);
        $this->assertSame('2024-04-01', $job['datePosted']);
        $this->assertSame('2024-05-01', $job['validThrough']);
        $this->assertSame('Erfarenhet', $job['experienceRequirements']);
        $this->assertSame('Heltid', $job['employmentType']);
        $this->assertSame('Tillsvidareanställning', $job['workHours']);
        $this->assertSame('Tillsvidareanställning', $job['relevantOccupation']);
        $this->assertSame('https://recruit.visma.com/apply?id=1', $job['url']);
        $this->assertTrue($job['directApply']);
        $this->assertSame('Tillsvidare', $job['jobDuration']);
        $this->assertSame('Tillsvidare', $job['employmentDuration']);
        $this->assertSame('https://example.test/job', $job['readMoreUrl']);
        $this->assertSame('Förvaltningen', $job['hiringOrganization']['name']);
        $this->assertSame('Skolförvaltningen', $job['employmentUnit']['name']);
        $this->assertSame('Skåne', $job['employmentUnit']['address']['addressRegion']);
        $this->assertSame('Helsingborg', $job['employmentUnit']['address']['addressLocality']);
        $this->assertSame('Sverige', $job['employmentUnit']['address']['addressCountry']);
        $this->assertSame('Ada Lovelace', $job['applicationContact'][0]['name']);
        $this->assertSame('Chef', $job['applicationContact'][0]['contactType']);
        $this->assertSame('ada@example.test', $job['applicationContact'][0]['email']);
        $this->assertSame('0701234567', $job['applicationContact'][0]['telephone']);
        $this->assertStringContainsString('<h2>Om arbetsplatsen</h2>', $job['description']);
        $this->assertStringContainsString('<h2>Arbetsuppgifter</h2>', $job['description']);
        $this->assertStringContainsString('<h2>Kvalifikationer</h2>', $job['description']);
        $this->assertStringContainsString('<h2>Övrig information</h2>', $job['description']);
        $this->assertStringContainsString('<h3>Beskrivning</h3>', $job['description']);
        $this->assertStringContainsString('Yrkesbeskrivning från Visma', $job['description']);
        $this->assertStringContainsString(
            '<a href="https://example.test/info">https://example.test/info</a>',
            $job['description']
        );
        $this->assertSame(
            '<a href="https://example.test/info">https://example.test/info</a>',
            str_replace('Se ', '', $job['specialCommitments'])
        );
        $this->assertSame(VismaJobPostingTransform::CREATED_BY, $job['x-created-by']);
        $this->assertSame(32, strlen($job['@version']));
    }

    #[TestDox('uses 1753-01-01 as an empty start date and keeps the description')]
    public function testSentinelStartDateKeepsDescriptionOnly(): void
    {
        $transform = $this->transformWithItem(<<<'XML'
            <Assignment>
                <EmploymentStartDate>1753-01-01T00:00:00</EmploymentStartDate>
                <Localization>
                    <AssignmentLoc>
                        <EmploymentStartDateDescr>Snarast</EmploymentStartDateDescr>
                        <DepartmentDescr></DepartmentDescr>
                        <WorkDescr></WorkDescr>
                        <Qualifications></Qualifications>
                    </AssignmentLoc>
                </Localization>
            </Assignment>
            XML);

        $jobs = $transform->transform(['content' => $this->listXml('1753-01-01T00:00:00')]);

        $this->assertSame('Snarast', $jobs[0]['jobStartDate']);
    }

    #[TestDox('throws when the list feed has no XML content')]
    public function testMissingContentThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new VismaJobPostingTransform('group-id'))->transform(['content' => '']);
    }

    #[TestDox('throws when Visma reports an unknown group')]
    public function testUnknownGroupThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new VismaJobPostingTransform('group-id'))->transform([
            'content' => '<Error>Kunde inte hitta gruppen</Error>',
        ]);
    }

    #[TestDox('returns an empty list when the feed has no assignments')]
    public function testEmptyFeedReturnsEmptyArray(): void
    {
        $transform = new VismaJobPostingTransform('group-id');

        $this->assertSame([], $transform->transform(['content' => '<Assignments></Assignments>']));
    }

    /**
     * Build a transform whose detail request returns a fixed item feed.
     */
    private function transformWithItem(string $itemXml): VismaJobPostingTransform
    {
        return new class ('group-id', $itemXml) extends VismaJobPostingTransform {
            public function __construct(string $guidGroup, private string $itemXml)
            {
                parent::__construct($guidGroup);
            }

            protected function fetchXml(string $url): string
            {
                return $this->itemXml;
            }
        };
    }

    /**
     * Assignment list XML. The start date can be overridden for the sentinel case.
     */
    private function listXml(string $employmentStartDate = '2024-06-01T00:00:00'): string
    {
        return <<<XML
            <Assignments>
                <Assignment>
                    <Guid>GUID-1</Guid>
                    <RefNo>C100</RefNo>
                    <AccountName>Förvaltningen</AccountName>
                    <NumberOfJobs>2</NumberOfJobs>
                    <PublishStartDate>2024-04-01T00:00:00</PublishStartDate>
                    <ApplicationEndDate>2024-05-01T00:00:00</ApplicationEndDate>
                    <EmploymentStartDate>{$employmentStartDate}</EmploymentStartDate>
                    <EmploymentEndDate>1753-01-01T00:00:00</EmploymentEndDate>
                    <Localization>
                        <AssignmentLoc>
                            <AssignmentTitle>Lärare</AssignmentTitle>
                            <WorkDescr>Lista arbete</WorkDescr>
                            <EmploymentGrade><Name>Deltid</Name></EmploymentGrade>
                            <EmploymentType><Name>Tillsvidareanställning</Name></EmploymentType>
                            <WorkExperiencePrerequisite><Name>Erfarenhet</Name></WorkExperiencePrerequisite>
                            <EmploymentStartDateDescr>Enligt överenskommelse</EmploymentStartDateDescr>
                            <EmploymentDuration><Name>Tillsvidare</Name></EmploymentDuration>
                            <County><Name>Skåne</Name></County>
                            <Municipality><Name>Helsingborg</Name></Municipality>
                            <Country><Name>Sverige</Name></Country>
                            <Departments>
                                <Department Type="Owner"><Name>Skolförvaltningen</Name></Department>
                            </Departments>
                        </AssignmentLoc>
                    </Localization>
                </Assignment>
            </Assignments>
            XML;
    }
}
