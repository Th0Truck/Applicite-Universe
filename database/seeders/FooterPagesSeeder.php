<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CmsPage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Create editable footer pages without overwriting existing CMS content.
 */
class FooterPagesSeeder extends Seeder
{
    /**
     * Persist missing pages and their initial content atomically.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            foreach ($this->pages() as $slug => $content) {
                $page = CmsPage::firstOrCreate(['slug' => $slug], [
                    'title' => $content['title'],
                    'template' => 'standard',
                    'is_published' => true,
                ]);

                if ($page->wasRecentlyCreated && $content['body'] !== '') {
                    $page->paragraphs()->create(['sort_order' => 0, 'heading' => '', 'body' => $content['body']]);
                }
            }
        });
    }

    /**
     * Supply starter text with explicit placeholders for unknown business details.
     *
     * @return array<string, array{title: string, body: string}>
     */
    private function pages(): array
    {
        return [
            'about' => ['title' => 'About', 'body' => ''],
            'privacy-policy' => [
                'title' => 'Privacy Policy',
                'body' => <<<'HTML'
<p>Generisk skabelon: Felterne i kantede parenteser skal tilpasses
den faktiske behandling af personoplysninger.</p>
<h3>Dataansvarlig og kontakt</h3>
<p>Universe drives af [virksomhedsnavn, adresse og eventuelt CVR-nummer].
Spørgsmål om personoplysninger kan sendes til [kontakt-e-mail].</p>
<h3>Oplysninger, formål og grundlag</h3>
<p>Ved brug af hjemmesiden kan vi behandle konto- og kontaktoplysninger, brugerindhold samt tekniske oplysninger.
[Angiv de konkrete oplysninger, formål og retsgrundlaget for hver behandling,
herunder eventuelle legitime interesser].</p>
<h3>Cookies</h3>
<p>[Beskriv anvendte cookies, deres formål og levetid samt hvordan eventuelt samtykke gives og trækkes tilbage].</p>
<h3>Modtagere og opbevaring</h3>
<p>[Angiv modtagere og databehandlere, eventuelle overførsler uden for EU/EØS og deres grundlag].
[Angiv opbevaringsperioder eller kriterier for sletning for hver kategori af oplysninger].</p>
<h3>Dine rettigheder</h3>
<p>Afhængigt af behandlingen kan du have ret til indsigt, rettelse, sletning, begrænsning,
indsigelse og dataportabilitet. Et samtykke kan trækkes tilbage uden at ændre lovligheden af tidligere behandling.
Kontakt os via ovenstående adresse. Du kan klage til Datatilsynet.</p>
<h3>Yderligere oplysninger</h3>
<p>[Angiv om oplysninger er nødvendige for en aftale eller et lovkrav og konsekvenserne ved ikke at give dem.
Beskriv eventuelle automatiske afgørelser eller profilering].</p>
<p>Senest opdateret: [dato].</p>
HTML,
            ],
            'terms-of-service' => [
                'title' => 'Terms of Service',
                'body' => <<<'HTML'
<p>Generisk skabelon: Tilpas vilkårene og felterne i kantede parenteser til den konkrete tjeneste.</p>
<h3>Om tjenesten</h3>
<p>Universe drives af [virksomhedsnavn, adresse og eventuelt CVR-nummer].
Kontakt: [kontakt-e-mail]. Hjemmesiden giver adgang til offentliggjorte sider og billedgallerier.
[Beskriv eventuelle yderligere tjenester og særlige betingelser].</p>
<h3>Brug af hjemmesiden</h3>
<p>Du skal bruge hjemmesiden lovligt og respektere andres rettigheder.
Du må ikke forsøge at få uautoriseret adgang, sprede skadelig software eller forstyrre driften.</p>
<h3>Konto og brugerindhold</h3>
<p>Hvis du opretter en konto, skal du beskytte dine loginoplysninger og kontakte os ved mistanke om misbrug.
Du må kun uploade indhold, som du har ret til at dele. Indhold må ikke krænke andres rettigheder.
[Beskriv eventuelle brugsrettigheder til indhold samt proceduren for moderation og lukning af konti].</p>
<h3>Rettigheder til materialet</h3>
<p>Tekster, billeder og øvrigt materiale kan være beskyttet af ophavsret.
Brug ud over det, som loven eller en angivet licens tillader, kræver rettighedshaverens tilladelse.</p>
<h3>Drift og ansvar</h3>
<p>Hjemmesiden kan være utilgængelig under vedligeholdelse eller tekniske fejl.
Ansvar vurderes efter gældende ret. Vilkårene begrænser ikke ufravigelige rettigheder.</p>
<h3>Personoplysninger og eksterne links</h3>
<p>Læs vores Privacy Policy for oplysninger om behandling af personoplysninger.
Eksterne hjemmesider har deres egne vilkår og privatlivspolitikker.</p>
<h3>Ændringer og henvendelser</h3>
<p>[Beskriv hvordan brugere informeres om væsentlige ændringer, og hvornår de træder i kraft].
Spørgsmål og klager kan sendes til kontaktadressen ovenfor.</p>
<p>Senest opdateret: [dato].</p>
HTML,
            ],
        ];
    }
}
