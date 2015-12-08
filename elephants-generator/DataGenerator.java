/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package elephantsgenerator;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;
import java.util.Locale;
import java.util.Random;

public class DataGenerator {
    
    
 
    private static String getRandomDecimal(final Random random,
        final int lowerBound,
        final int upperBound,
        final int decimalPlaces){

        if(lowerBound < 0 || upperBound <= lowerBound || decimalPlaces < 0){
            throw new IllegalArgumentException("Please, enter valid bound");
        }

        final double dbl =
            ((random == null ? new Random() : random).nextDouble() 
                * (upperBound - lowerBound))
                + lowerBound;
        //locale US gives float with '.',instead of ','
        return String.format(Locale.US,"%." + decimalPlaces + "f", dbl);
    }
    
    private static int getRandomInteger(int lowerBound,int upperBound){
        return Integer.parseInt(DataGenerator.getRandomDecimal(null, lowerBound, upperBound, 0));
    }
    // returns array of elephants sizes 
    public static List<String> generateSizes(){
        
        List<String> sizes = new ArrayList<>(Arrays.asList(sizesArr));
        int numSizes = DataGenerator.getRandomInteger(1, sizes.size() );
        //remove random elements from size array, while its size != random number of sizedgenerated
        while(sizes.size()!= numSizes){
            int randIndex = DataGenerator.getRandomInteger(0, sizes.size()-1 );
            sizes.remove(randIndex);
        }
        return sizes;
    }
    
     public static String generateDescription(int minSize,int maxSize){
        if(lorem.length() < maxSize || lorem.length() < minSize){
            throw new IllegalArgumentException("size is smaller, then text!");
        }
        int start = DataGenerator.getRandomInteger(0, lorem.length() - maxSize);
        int end = start + DataGenerator.getRandomInteger(minSize, maxSize);
        String text = lorem.substring(start, end);
        return text;
    }
    
    public static String generateNameWithSize(){
        int namesLen = DataGenerator.listOfElephantNames.length-1;
        int sizesLen = DataGenerator.sizeIndicators.length-1;
        int nameArrIndex = Integer.parseInt(DataGenerator.getRandomDecimal(new Random(), 0, namesLen, 0));
        int sizesArrIndex =  Integer.parseInt(DataGenerator.getRandomDecimal(new Random(), 0, sizesLen, 0));
        String nameWithSize = DataGenerator.sizeIndicators[sizesArrIndex];
        nameWithSize+=" ";
        nameWithSize += DataGenerator.listOfElephantNames[nameArrIndex];
        return nameWithSize;
    }

    
    //generate query for main table of elephants
    public static String getProductsTableSQL(String tableName,int id,String name,String img,String category){
        String sql =  "INSERT INTO "+'`'+tableName+'`'+"(id,name,price,img,category,description,quantity) "
                + "VALUES("
                +id+','
                +'"'+name+'"'+','
                +DataGenerator.getRandomDecimal(null, 50, 5000, 2)+','
                +'"'+img+'"'+','
                +'"'+category+'"'+','
                +'"'+DataGenerator.generateDescription(15,100)+'"'+','
                +DataGenerator.getRandomInteger(0,500)
                +");\n";
        return sql;
    }
    //generate query for related sizes table of elephant
    public static String getSizesTableRowSQL(String tableName,int id,int product_id,String size){
          return "INSERT INTO "+'`'+tableName+'`'+"(id,product_id,size) "
                + "VALUES("
                +id+','
                +product_id+','
                +'"'+size+'"'
                +");\n";
    }

    
    public static String[] sizesArr = {"very_big","big","medium","small","very_small"};
    
//translated from https://en.wikipedia.org/wiki/List_of_individual_elephants
     public static String[] listOfElephantNames = {
      "Абу-ль-Аббас", "Баларама", "Батир", "Чорний діамант", "Кастор", "Поллукс", "Чуні",
         "Кремонан", "Дронна","Ехо","Фанні","Ханно","Ханскен","Хетті","Джон Л. Салліван",
         "Джамбо","Кандулл","Кесаван","Колаколлі","Руук","Лінь Ван",
         "Мотті", "Старий", "Усама бен Ладен", "Пакі", "Куїн", "Куїн", "Раджа",
         "Раджа Гай", "Раджі", "Роззі", "Рубін", "Сіль", "Соус", "Сулейман", "Сурус", "Тай", "Шиворот",
         "Туффі", "Туско", "Тайк", "Зіггі"
    };
     
    public static String[] sizeIndicators = {
      "Астрономічний", "Безмежний", "Широкий", "Мускулистий", "Місткий", "Кремезний", "Колосальний",
        "Значний", "Ряснй", "Товстий", "Глибокий", "Величезний",
        "Епічний", "Експансивний", "Великий", "Жирний", "Повний", "Гігант",
        "Гігантський", "Грандіозний", "Великий", "Здоровенний", "Важкий",
        "Вражаючий", "Король розміру", "Безмежний", "Мамонт", "Масивний", "Мега", "Могутній", "Широченний", "Монстрисько",
        "Монументальний", "Гороподібний", "Безліч-великий", 
        "Ожирівший", "Габаритний", "Надлишково-великий", "Великогабаритний",
        "Товстий", "Опрятний", "Потужний", "Жахливий", "Значний", "Просторий", "Істотна", "Високий", "Щільний", "Титанік",
        "Поднебесний", "Величезний", "Безлімітний", "Об'ємний", "Вагомий", "колосальний"
    };
    
    
    public static String lorem = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed tamen nonne reprehenderes, Epicure,"
            + " luxuriosos ob eam ipsam causam, quod ita viverent, ut persequerentur cuiusque modi voluptates, cum esset praesertim, "
            + "ut ais tu, summa voluptas nihil dolere? Duo Reges: constructio interrete. Esse tantam vim virtutis tantamque, ut ita dicam,"
            + " auctoritatem honestatis, ut reliqua non illa quidem nulla, sed ita parva sint, ut nulla esse videantur. An obliviscimur, "
            + "quantopere in audiendo in legendoque moveamur, cum pie, cum amice, cum magno animo aliquid factum cognoscimus? Quae dici eadem de ceteris"
            + " virtutibus possunt, quarum omnium fundamenta vos in voluptate tamquam in aqua ponitis. Crassus fuit, qui tamen solebat uti suo bono,"
            + " ut hodie est noster Pompeius, cui recte facienti gratia est habenda; Tollenda est atque extrahenda radicitus. "
            + "Sin kakan malitiam dixisses, ad aliud nos unum certum vitium consuetudo Latina traduceret. "
            + "Quem enim ardorem studii censetis fuisse in Archimede, qui dum in pulvere quaedam describit attentius, "
            + "ne patriam quidem captam esse senserit? Sine ea igitur iucunde negat posse se vivere?Id autem eius modi est, "
            + "ut additum ad virtutem auctoritatem videatur habiturum et expleturum cumulate vitam beatam, de quo omnis haec quaestio est. "
            + "Honestum igitur id intellegimus, quod tale est, ut detracta omni utilitate sine ullis praemiis fructibusve per se ipsum possit iure laudari. "
            + "Et harum quidem rerum facilis est et expedita distinctio. Me ipsum esse dicerem, inquam, "
            + "nisi mihi viderer habere bene cognitam voluptatem et satis firme conceptam animo atque comprehensam."
            + " Vadem te ad mortem tyranno dabis pro amico, ut Pythagoreus ille Siculo fecit tyranno? "
            + "Ut enim, inquit, gubernator aeque peccat, si palearum navem evertit et si auri, item aeque peccat,"
            + " qui parentem et qui servum iniuria verberat. Si ad prudentes, alterum fortasse dubitabunt, sitne tantum in virtute,"
            + " ut ea praediti vel in Phalaridis tauro beati sint, alterum non dubitabunt, quin et Stoici conveniente sibi dicant et vos repugnantia."
            + " Primum non saepe, deinde quae est ista relaxatio, cum et praeteriti doloris memoria recens est et futuri atque inpendentis torquet timor?"
            + " Ab hoc autem quaedam non melius quam veteres, quaedam omnino relicta. Qui autem diffidet perpetuitati bonorum suorum, timeat necesse est, "
            + "ne aliquando amissis illis sit miser. Atque etiam ad iustitiam colendam, ad tuendas amicitias et reliquas caritates quid natura valeat haec una cognitio potest tradere. "
            + "Atque etiam valítudinem, vires, vacuitatem doloris non propter utilitatem solum, sed etiam ipsas propter se expetemus."
            + "Si una virtus, unum istud, quod honestum appellas, rectum, laudabile, decorumerit enim notius quale sit pluribus notatum vocabulis idem declarantibus, "
            + "id ergo, inquam, si solum est bonum, quid habebis praeterea, quod sequare? Quis est enim, in quo sit cupiditas, quin recte cupidus dici possit? "
            + "Sensibus enim ornavit ad res percipiendas idoneis, ut nihil aut non multum adiumento ullo ad suam confirmationem indigerent; Hic Speusippus, hic Xenocrates, hic eius auditor Polemo, cuius illa ipsa sessio fuit,"
            + " quam videmus. Quod quidem mihi si quando dictum est-est autem dictum non parum saepe-, etsi satis clemens sum in disputando, tamen interdum soleo subirasci."
            + "Quis negat? Nam ex his tribus laudibus, quas ante dixi, et temeritatem reformidat et non audet cuiquam aut dicto protervo aut facto nocere vereturque quicquam "
            + "aut facere aut eloqui, quod parum virile videatur. Crasso, quem semel ait in vita risisse Lucilius, non contigit, ut ea re minus agelastoj ut ait idem, "
            + "vocaretur. Quam vellem, inquit, te ad Stoicos inclinavisses! erat enim, si cuiusquam, certe tuum nihil praeter virtutem in bonis ducere. "
            + "Itaque ne iustitiam quidem recte quis dixerit per se ipsam optabilem, sed quia iucunditatis vel plurimum afferat."
            + " Nam omnia, quae sumenda quaeque legenda aut optanda sunt, inesse debent in summa bonorum, ut is, qui eam adeptus sit, nihil praeterea desideret."
            + " Quid enim dicis omne animal, simul atque sit ortum, applicatum esse ad se diligendum esseque in se conservando occupatum?"
            + " His enim rebus detractis negat se reperire in asotorum vita quod reprehendat. Atque hoc dabitis, ut opinor, si modo sit aliquid esse beatum,"
            + " id oportere totum poni in potestate sapientis. Atque in sensibus est sua cuiusque virtus, "
            + "ut ne quid impediat quo minus suo sensus quisque munere fungatur in iis rebus celeriter expediteque percipiendis, quae subiectae sunt sensibus."
            + "Ego autem existimo, si honestum esse aliquid ostendero, quod sit ipsum vi sua propter seque expetendum, iacere vestra omnia. "
            + "Ut ad minora veniam, mathematici, poëtae, musici, medici denique ex hac tamquam omnium artificum officina profecti sunt."
            + " Sed hoc interest, quod voluptas dicitur etiam in animo-vitiosa res, ut Stoici putant, qui eam sic definiunt: "
            + "sublationem animi sine ratione opinantis se magno bono frui-, non dicitur laetitia nec gaudium in corpore. "
            + "Nec vero dico eorum metum mortis, qui, quia privari se vitae bonis arbitrentur, aut quia quasdam post mortem formidines extimescant, "
            + "aut si metuant, ne cum dolore moriantur, idcirco mortem fugiant; Quae rursus dum sibi evelli ex ordine nolunt, "
            + "horridiores evadunt, asperiores, duriores et oratione et moribus. Conveniret, pluribus praeterea conscripsisset qui esset optimus rei publicae status,"
            + " hoc amplius Theophrastus: quae essent in re publica rerum inclinationes et momenta temporum, quibus esset moderandum, utcumque res postularet. "
            + "Ergo opifex plus sibi proponet ad formarum quam civis excellens ad factorum pulchritudinem? Nam si dicent ab illis has res esse tractatas, "
            + "ne ipsos quidem Graecos est cur tam multos legant, quam legendi sunt. Itaque et vivere vitem et mori dicimus arboremque et novellan et vetulam et vigere et senescere."
            + " In homine autem summa omnis animi est et in animo rationis, ex qua virtus est, quae rationis absolutio definitur, quam etiam atque etiam explicandam putant. "
            + "Quis autem de ipso sapiente aliter existimat, quin, etiam cum decreverit esse moriendum, tamen discessu a suis atque ipsa relinquenda luce moveatur? "
            + "Praeclare enim Plato: Beatum, cui etiam in senectute contigerit, ut sapientiam verasque opiniones assequi possit. "
            + "E quo efficitur, non ut nos non intellegamus quae vis sit istius verbi, sed ut ille suo more loquatur, nostrum neglegat. "
            + "Quae quidem adhuc peregrinari Romae videbatur nec offerre sese nostris sermonibus, et ista maxime propter limatam quandam et rerum et verborum tenuitatem. "
            + "Aderamus nos quidem adolescentes, sed multi amplissimi viri, quorum nemo censuit plus Fadiae dandum, quam posset ad eam lege Voconia pervenire. "
            + "Ut bacillum aliud est inflexum et incurvatum de industria, aliud ita natum, sic ferarum natura non est illa quidem depravata mala disciplina, "
            + "sed natura sua. Idem etiam dolorem saepe perpetiuntur, ne, si id non faciant, incidant in maiorem. Quodsi esset in voluptate summum bonum, "
            + "ut dicitis, optabile esset maxima in voluptate nullo intervallo interiecto dies noctesque versari, cum omnes sensus dulcedine omni quasi perfusi moverentur. "
            + "Ut iam liceat una comprehensione omnia complecti non dubitantemque dicere omnem naturam esse servatricem sui idque habere propositum quasi finem et extremum, "
            + "se ut custodiat quam in optimo sui generis statu; Quoniamque in iis rebus, quae neque in virtutibus sunt neque in vitiis, est tamen quiddam, quod usui possit esse, tollendum id non est. "
            + "Ita fit, ut duo genera propter se expetendorum reperiantur, unum, quod est in iis, in quibus completar illud extremum, quae sunt aut animi aut corporis; Ipse enim Metrodorus, paene alter Epicurus,"
            + " beatum esse describit his fere verbis: cum corpus bene constitutum sit et sit exploratum ita futurum. Ut ad minora veniam, mathematici, poëtae, musici, medici denique ex hac tamquam omnium artificum officina profecti sunt. "
            + "Cum autem dispicere coepimus et sentire quid, simus et quid ab animantibus ceteris differamus, tum ea sequi incipimus, ad quae nati sumus.";
    
    
    //original elephant names and size indicators
    
      /*  public static String[] listOfElephantNames = {
        "Abul-Abbas","Balarama","Batyr","Black Diamond","Castor","Pollux","Chunee",
        "Cremonan","Drona", "Echo","Fanny","Hanno","Hansken","Hattie","John L. Sullivan",
        "Jumbo","Kandula","Kesavan","Kolakolli","Lallah Rookh","Lizzie","Lin Wang","Mary",
        "Mona","Motty","Old Bet","Osama bin Laden","Packy","Queenie","Queenie","Raja",
        "Raja Gaj","Rajje","Rosie","Ruby","Salt","Sauce","Suleiman","Surus","Tai","Topsy",
        "Tuffi","Tusko","Tyke","Ziggy"
    };*/
    
    /*public static String[] sizeIndicators = {
        "Astronomical", "Boundless", "Broad", "Brawny", "Capacious", "Chunky", "Colossal",
        "Considerable", "Copious", "Corpulent", "Deep", "Elephantine", "Enormous", 
        "Epic", "Expansive", "Extensive", "Fat", "Full", "Gargantuan", "Giant", 
        "Gigantic", "Ginormous", "Goodly", "Grand", "Great", "Heaping", "Heavy",
        "Hefty", "Herculean", "Huge", "Hulking", "Humongous", "Husky", "Immense",
        "Imposing", "Impressive", "Infinite", "King-sized", "Large", "Limitless",
        "Lofty", "Mammoth", "Massive", "Mega", "Mighty", "Miles-wide", "Monsterous",
        "Monumental", "Mountainous", "Multifarious", "Multitude", "Multiplicity",
        "Numerous", "Obese", "Outsized", "Overabundant", "Oversized", "Plentiful", 
        "Plump", "Portly", "Powerful", "Prodigious", "Sizable", "Spacious", "Stout",
        "Strapping", "Substantial", "Sweeping", "Tall", "Thick", "Thick-set", "Titanic",
        "Towering", "Tremendous", "Unlimited", "Vast", "Voluminous", "Weighty", "Whopping", "Wide"
    };*/
}