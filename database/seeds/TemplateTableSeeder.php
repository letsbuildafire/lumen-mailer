<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Template;

class TemplateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
     public function run()
     {

        Template::create([
            'name' => 'Simple Text',
            'source' => 'first',
            'default_content' => '<h1>An eum discere ea mavis, quae cum plane perdidiceriti nihil sciat?</h1><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed in rebus apertissimis nimium longi sumus. Quae sequuntur igitur? Numquam facies. Aliter enim explicari, quod quaeritur, non potest. Duo Reges: constructio interrete. Tu quidem reddes; Et quidem, inquit, vehementer errat; Mihi enim satis est, ipsis non satis. </p><p>Quid ait Aristoteles reliquique Platonis alumni? Tamen a proposito, inquam, aberramus. ALIO MODO. Confecta res esset. Qualem igitur hominem natura inchoavit? </p><p>Scaevolam M. <i>Tecum optime, deinde etiam cum mediocri amico.</i> <i>Non est igitur voluptas bonum.</i> <i>Quo tandem modo?</i> </p><ul><li>Unum nescio, quo modo possit, si luxuriosus sit, finitas cupiditates habere.</li><li>Philosophi autem in suis lectulis plerumque moriuntur.</li><li>Septem autem illi non suo, sed populorum suffragio omnium nominati sunt.</li><li>Quae hic rei publicae vulnera inponebat, eadem ille sanabat.</li><li>Oratio me istius philosophi non offendit;</li><li>Si enim ad populum me vocas, eum.</li></ul><p>Sed fortuna fortis; Tum ille: Ain tandem? </p><p>Haec dicuntur inconstantissime. Perge porro; At enim hic etiam dolore. At hoc in eo M. </p><dl><dt><dfn>Nunc vides, quid faciat.</dfn></dt><dd>Quod enim dissolutum sit, id esse sine sensu, quod autem sine sensu sit, id nihil ad nos pertinere omnino.</dd><dt><dfn>Magna laus.</dfn></dt><dd>Ut optime, secundum naturam affectum esse possit.</dd></dl>'
        ]);  

        Template::create([
            'name' => 'Single Column',
            'source' => 'second',
            'default_content' => '<h1>An eum discere ea mavis, quae cum plane perdidiceriti nihil sciat?</h1><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed in rebus apertissimis nimium longi sumus. Quae sequuntur igitur? Numquam facies. Aliter enim explicari, quod quaeritur, non potest. Duo Reges: constructio interrete. Tu quidem reddes; Et quidem, inquit, vehementer errat; Mihi enim satis est, ipsis non satis. </p><p>Quid ait Aristoteles reliquique Platonis alumni? Tamen a proposito, inquam, aberramus. ALIO MODO. Confecta res esset. Qualem igitur hominem natura inchoavit? </p><p>Scaevolam M. <i>Tecum optime, deinde etiam cum mediocri amico.</i> <i>Non est igitur voluptas bonum.</i> <i>Quo tandem modo?</i> </p><ul><li>Unum nescio, quo modo possit, si luxuriosus sit, finitas cupiditates habere.</li><li>Philosophi autem in suis lectulis plerumque moriuntur.</li><li>Septem autem illi non suo, sed populorum suffragio omnium nominati sunt.</li><li>Quae hic rei publicae vulnera inponebat, eadem ille sanabat.</li><li>Oratio me istius philosophi non offendit;</li><li>Si enim ad populum me vocas, eum.</li></ul><p>Sed fortuna fortis; Tum ille: Ain tandem? </p><p>Haec dicuntur inconstantissime. Perge porro; At enim hic etiam dolore. At hoc in eo M. </p><dl><dt><dfn>Nunc vides, quid faciat.</dfn></dt><dd>Quod enim dissolutum sit, id esse sine sensu, quod autem sine sensu sit, id nihil ad nos pertinere omnino.</dd><dt><dfn>Magna laus.</dfn></dt><dd>Ut optime, secundum naturam affectum esse possit.</dd></dl>'
        ]);  

        Template::create([
            'name' => 'Two Columns',
            'source' => 'third',
            'default_content' => '<h1>An eum discere ea mavis, quae cum plane perdidiceriti nihil sciat?</h1><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed in rebus apertissimis nimium longi sumus. Quae sequuntur igitur? Numquam facies. Aliter enim explicari, quod quaeritur, non potest. Duo Reges: constructio interrete. Tu quidem reddes; Et quidem, inquit, vehementer errat; Mihi enim satis est, ipsis non satis. </p><p>Quid ait Aristoteles reliquique Platonis alumni? Tamen a proposito, inquam, aberramus. ALIO MODO. Confecta res esset. Qualem igitur hominem natura inchoavit? </p><p>Scaevolam M. <i>Tecum optime, deinde etiam cum mediocri amico.</i> <i>Non est igitur voluptas bonum.</i> <i>Quo tandem modo?</i> </p><ul><li>Unum nescio, quo modo possit, si luxuriosus sit, finitas cupiditates habere.</li><li>Philosophi autem in suis lectulis plerumque moriuntur.</li><li>Septem autem illi non suo, sed populorum suffragio omnium nominati sunt.</li><li>Quae hic rei publicae vulnera inponebat, eadem ille sanabat.</li><li>Oratio me istius philosophi non offendit;</li><li>Si enim ad populum me vocas, eum.</li></ul><p>Sed fortuna fortis; Tum ille: Ain tandem? </p><p>Haec dicuntur inconstantissime. Perge porro; At enim hic etiam dolore. At hoc in eo M. </p><dl><dt><dfn>Nunc vides, quid faciat.</dfn></dt><dd>Quod enim dissolutum sit, id esse sine sensu, quod autem sine sensu sit, id nihil ad nos pertinere omnino.</dd><dt><dfn>Magna laus.</dfn></dt><dd>Ut optime, secundum naturam affectum esse possit.</dd></dl>'
        ]);

    }
}
