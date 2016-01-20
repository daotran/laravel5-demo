-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 20, 2016 at 10:56 AM
-- Server version: 5.5.46
-- PHP Version: 5.6.17-1+deb.sury.org~precise+2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `laravel5-demo`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `seen` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL,
  `post_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `comments_user_id_foreign` (`user_id`),
  KEY `comments_post_id_foreign` (`post_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `created_at`, `updated_at`, `content`, `seen`, `user_id`, `post_id`) VALUES
(1, '2015-10-18 23:19:25', '2015-10-18 23:19:25', '<p>\nLorem ipsum leo integer nunc cras tristique nibh class vehicula habitant, hendrerit mattis erat porta molestie platea odio auctor vestibulum, inceptos auctor sollicitudin porttitor tincidunt eros torquent quisque mollis. \nNibh quis eu habitant class augue varius curabitur a, dictumst aliquam id euismod fermentum varius amet neque, dictumst phasellus lorem viverra sagittis rhoncus quisque. \nLeo molestie vivamus ut habitasse mattis, nunc imperdiet dui eget litora, euismod arcu augue est. \nAenean condimentum tincidunt aenean ut habitant congue sem lobortis etiam, urna aenean conubia nec at cubilia tincidunt pretium, mattis dictumst ad enim feugiat varius tristique blandit. \n</p>\n<p>\nInteger nullam volutpat imperdiet suscipit semper aliquam rhoncus iaculis dolor, turpis ac libero aliquam elit sem vehicula id lobortis, eget consequat aliquam conubia et placerat imperdiet egestas. \nRutrum consectetur a nisi nam faucibus sit sodales ut, viverra velit auctor quam luctus ut amet, integer porttitor odio interdum volutpat enim pharetra. \nTristique porta bibendum imperdiet curabitur facilisis dictumst conubia rutrum elementum, sem vehicula placerat proin etiam nulla quisque mattis, donec odio vulputate nostra sit non bibendum ullamcorper. \nDapibus congue tincidunt arcu diam aenean blandit nam donec metus eleifend vulputate tempor fringilla interdum himenaeos, quam lacus venenatis euismod inceptos nibh maecenas venenatis odio vivamus quam pulvinar porttitor mi. \n</p>', 0, 2, 1),
(2, '2015-10-18 23:19:25', '2015-10-18 23:19:25', '<p>\nLorem ipsum pharetra platea primis elementum pretium id class taciti ligula, platea inceptos semper nec sit dolor iaculis fames justo consequat, vulputate aliquam habitant pretium congue dolor tempor maecenas bibendum. \nTaciti scelerisque ipsum mattis himenaeos dui lacinia dictumst class vivamus fermentum, neque pellentesque ullamcorper ut sagittis eleifend massa torquent volutpat sem fringilla, porta scelerisque taciti aenean placerat etiam porttitor in commodo. \nPlacerat dui at sociosqu dictum semper eu conubia augue amet, imperdiet posuere libero rutrum facilisis nec iaculis luctus, vitae libero mattis class nulla ut sed magna. \nFeugiat congue feugiat dictum per volutpat netus tempor lacus ut, blandit primis nunc posuere integer enim ligula consectetur, hac torquent rutrum aptent fermentum consectetur ac neque. \n</p>\n<p>\nSed ut lacus volutpat vehicula lacus mi, at habitasse faucibus donec auctor sodales, nibh massa per tristique viverra. \nVestibulum suspendisse sociosqu lectus at curabitur, porttitor leo blandit enim, rhoncus himenaeos ornare sem. \nVestibulum vivamus ante mi vehicula lacinia ac porttitor molestie urna, leo purus suscipit taciti augue lacinia elit ac, elementum nisl iaculis felis magna habitasse sed interdum. \nFames iaculis diam tellus litora primis etiam risus vel, congue gravida sollicitudin sagittis fusce potenti adipiscing urna, dolor id vitae imperdiet metus rutrum dapibus. \nAliquam aenean vulputate vitae, augue. \n</p>', 0, 2, 2),
(3, '2015-10-18 23:19:25', '2015-10-18 23:19:25', '<p>\nLorem ipsum porttitor mauris tempus elit dui sodales curabitur non, quisque curabitur rutrum mattis sollicitudin pellentesque risus. \nUltrices fringilla mollis pretium cubilia quam commodo elementum tellus platea justo, cubilia aenean faucibus lacus diam pulvinar semper nisl semper mollis lacus, ligula sed eleifend amet nec facilisis porta felis dictumst. \nPlatea morbi taciti ultricies et euismod orci pulvinar, hendrerit egestas lorem habitasse gravida aenean mauris curabitur, integer turpis sollicitudin congue sollicitudin suspendisse. \nGravida pretium hendrerit dapibus torquent donec himenaeos pulvinar lectus, urna tempus nisl quisque sodales enim mauris vehicula, donec urna etiam morbi dictumst a proin. \n</p>\n<p>\nEt sem auctor et vitae at diam tristique maecenas mi, leo lacinia dictum maecenas conubia urna eget. \nLacus tristique dolor blandit bibendum quis gravida ultricies, viverra curabitur integer facilisis leo et morbi inceptos, tristique lorem est ac suscipit dictumst. \nPorta accumsan blandit metus etiam senectus eleifend est etiam, netus id mattis lorem metus pretium fringilla leo, congue sagittis quisque dictumst id cursus in. \nPurus non habitasse hac augue mauris aenean fusce elementum sollicitudin litora sociosqu nulla primis habitasse duis odio dictum ante, arcu curae etiam rhoncus congue dolor at condimentum netus placerat hac tempor placerat turpis consequat laoreet posuere. \n</p>\n<p>\nAliquam dictum nibh quam eu, proin diam. \n</p>', 0, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE IF NOT EXISTS `contacts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `seen` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `text`, `seen`, `created_at`, `updated_at`) VALUES
(1, 'Dupont', 'dupont@la.fr', 'Lorem ipsum inceptos malesuada leo fusce tortor sociosqu semper, facilisis semper class tempus faucibus tristique duis eros, cubilia quisque habitasse aliquam fringilla orci non. Vel laoreet dolor enim justo facilisis neque accumsan, in ad venenatis hac per dictumst nulla ligula, donec mollis massa porttitor ullamcorper risus. Eu platea fringilla, habitasse.', 0, '2015-10-18 23:19:24', '2015-10-18 23:19:24'),
(2, 'Durand', 'durand@la.fr', ' Lorem ipsum erat non elit ultrices placerat, netus metus feugiat non conubia fusce porttitor, sociosqu diam commodo metus in. Himenaeos vitae aptent consequat luctus purus eleifend enim, sollicitudin eleifend porta malesuada ac class conubia, condimentum mauris facilisis conubia quis scelerisque. Lacinia tempus nullam felis fusce ac potenti netus ornare semper molestie, iaculis fermentum ornare curabitur tincidunt imperdiet scelerisque imperdiet euismod.', 0, '2015-10-18 23:19:24', '2015-10-18 23:19:24'),
(3, 'Martin', 'martin@la.fr', 'Lorem ipsum tempor netus aenean ligula habitant vehicula tempor ultrices, placerat sociosqu ultrices consectetur ullamcorper tincidunt quisque tellus, ante nostra euismod nec suspendisse sem curabitur elit. Malesuada lacus viverra sagittis sit ornare orci, augue nullam adipiscing pulvinar libero aliquam vestibulum, platea cursus pellentesque leo dui. Lectus curabitur euismod ad, erat.', 1, '2015-10-18 23:19:24', '2015-10-18 23:19:24'),
(4, 'Tran Tien Dao', 'dao.tran@enclave.vn', 'Testing mail', 0, '2015-10-18 23:34:24', '2015-10-18 23:34:24'),
(5, 'Tran Tien Dao', 'dao.tran@enclave.vn', 'fsdgfdsgdfg', 0, '2015-10-19 00:26:20', '2015-10-19 00:26:20');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE IF NOT EXISTS `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2014_10_12_000000_create_users_table', 1),
('2014_10_12_100000_create_password_resets_table', 1),
('2014_10_21_105844_create_roles_table', 1),
('2014_10_21_110325_create_foreign_keys', 1),
('2014_10_24_205441_create_contact_table', 1),
('2014_10_26_172107_create_posts_table', 1),
('2014_10_26_172631_create_tags_table', 1),
('2014_10_26_172904_create_post_tag_table', 1),
('2014_10_26_222018_create_comments_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `password_resets_email_index` (`email`),
  KEY `password_resets_token_index` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `summary` text COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `seen` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `posts_slug_unique` (`slug`),
  KEY `posts_user_id_foreign` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `created_at`, `updated_at`, `title`, `slug`, `summary`, `content`, `seen`, `active`, `user_id`) VALUES
(1, '2015-10-18 23:19:25', '2015-10-18 23:19:25', 'Post 1', 'post-1', '<img alt="" src="/filemanager/userfiles/user2/mega-champignon.png" style="float:left; height:128px; width:128px" /><p>\nLorem ipsum ad ligula sed suspendisse blandit turpis nec eget et nam dapibus, aptent curae consequat taciti curae erat et ullamcorper sollicitudin aenean ultrices. \nRisus in dictum sem vivamus sed dictum ac euismod blandit curabitur fusce, aliquet semper vestibulum varius congue vehicula donec quisque lectus nisl. \nMattis imperdiet dolor, feugiat. \n</p>', '<p>\nLorem ipsum ad platea et diam congue convallis, velit class sapien quisque augue netus tristique, eros ante dui arcu amet nunc. \nSed arcu nisl inceptos fusce augue habitant enim aptent faucibus quisque, fermentum urna tempus a eros tellus magna ut. \nAptent nisi sem pharetra facilisis cras in felis per hendrerit, mattis curabitur mi tristique elit aliquet posuere vel congue, malesuada vehicula faucibus consectetur faucibus suscipit pulvinar habitant. \nEget duis aptent dictum amet sapien curabitur ante augue porttitor auctor pretium, luctus dapibus luctus mollis conubia donec vel felis molestie per. \n</p>\n<p>\nNullam arcu eget pellentesque morbi turpis varius odio inceptos id adipiscing libero, id donec lectus primis urna per orci quisque ipsum habitasse etiam, duis dapibus maecenas arcu aliquam litora ultrices torquent ullamcorper potenti. \nMaecenas nec sem nec aliquam arcu amet, aptent eu iaculis enim vestibulum ut pellentesque, curabitur justo lorem eleifend odio. \nEt curabitur donec phasellus aliquam fermentum cubilia erat quisque, lectus tincidunt tellus suscipit feugiat duis vulputate, suspendisse condimentum aenean dui netus vehicula tempor. \nSed nullam maecenas libero curabitur purus dictumst eros aenean nisl morbi, ipsum platea sociosqu donec sem accumsan ac tristique aenean orci, scelerisque inceptos ullamcorper suscipit lacinia ut tempor purus rhoncus. \n</p>\n<p>\nFaucibus donec ultricies non iaculis aliquam luctus consectetur donec quis, massa dapibus tempus hac litora cras cursus sodales imperdiet taciti, donec maecenas ultricies consectetur taciti nibh porttitor venenatis. \nElementum dui sit mauris integer amet aptent bibendum amet arcu a curabitur, orci class eu quisque turpis ligula lacus velit inceptos blandit. \nDonec condimentum rutrum nulla platea dapibus dui etiam adipiscing dapibus pulvinar, ante felis lacus fames augue sagittis etiam aptent volutpat quisque, bibendum nunc sed arcu morbi torquent etiam fames aliquet. \nSociosqu habitasse varius phasellus conubia dui nibh massa venenatis sociosqu velit, nulla bibendum habitasse facilisis suspendisse dapibus lobortis faucibus nibh inceptos, habitasse sodales pharetra placerat lorem lectus potenti primis pulvinar. \n</p>\n<p>\nBibendum curabitur vivamus eu fames enim himenaeos donec quis egestas scelerisque at eu, enim feugiat suscipit metus ligula donec euismod vitae nec primis inceptos. \nQuam dictumst potenti lacinia etiam nullam facilisis hac, lectus non libero curabitur nisl litora ut et, lectus nam nisl fermentum viverra vitae. \nSociosqu scelerisque per interdum lacus himenaeos nisi vestibulum facilisis quis nisi, augue cras porta inceptos orci varius ad facilisis lacinia, gravida dictumst platea convallis placerat gravida praesent euismod leo. \nPurus nibh sed tempor curabitur per nisl cursus fusce cras suscipit vehicula accumsan, quam suscipit nam conubia erat accumsan ut viverra praesent convallis eleifend, lacus lacinia hendrerit quisque duis etiam molestie est vulputate ad nulla. \n</p>\n<p>\nPorta lacus fermentum cursus netus vel molestie, ornare gravida quisque vivamus porta, risus nec eget phasellus tellus. \nPharetra ipsum magna semper nulla risus purus pretium fringilla, tellus rutrum metus pulvinar facilisis justo iaculis, mattis adipiscing nec amet enim netus volutpat. \nDonec facilisis duis conubia elit vivamus nisi nam, quis eleifend curabitur scelerisque sollicitudin fringilla, dolor a mollis donec integer netus. \nOrci fames aptent a amet ultrices netus convallis nulla nisl, vestibulum lobortis aliquam rhoncus at volutpat vivamus in, ut laoreet proin bibendum porta dictum proin aliquet. \n</p>', 0, 1, 1),
(2, '2015-10-18 23:19:25', '2015-10-18 23:19:25', 'Post 2', 'post-2', '<img alt="" src="/filemanager/userfiles/user2/goomba.png" style="float:left; height:128px; width:128px" /><p>\nLorem ipsum et laoreet et interdum inceptos molestie eu malesuada, eleifend vivamus metus volutpat vitae maecenas sem lectus tempus, gravida nisi enim congue nulla mi nibh a. \nCurabitur curae cursus hendrerit cubilia hac pellentesque ut etiam, consectetur volutpat sit praesent fusce convallis feugiat, vel augue eu nulla eros morbi ultrices. \n</p>', '<p>Lorem ipsum convallis ac curae non elit ultrices placerat netus metus feugiat, non conubia fusce porttitor sociosqu diam commodo metus in himenaeos, vitae aptent consequat luctus purus eleifend enim sollicitudin eleifend porta. Malesuada ac class conubia condimentum mauris facilisis conubia quis scelerisque lacinia, tempus nullam felis fusce ac potenti netus ornare semper. Molestie iaculis fermentum ornare curabitur tincidunt imperdiet scelerisque, imperdiet euismod scelerisque torquent curae rhoncus, sollicitudin tortor placerat aptent hac nec. Posuere suscipit sed tortor neque urna hendrerit vehicula duis litora tristique congue nec auctor felis libero, ornare habitasse nec elit felis inceptos tellus inceptos cubilia quis mattis faucibus sem non.</p>\n\n<p>Odio fringilla class aliquam metus ipsum lorem luctus pharetra dictum, vehicula tempus in venenatis gravida ut gravida proin orci, quis sed platea mi quisque hendrerit semper hendrerit. Facilisis ante sapien faucibus ligula commodo vestibulum rutrum pretium, varius sem aliquet himenaeos dolor cursus nunc habitasse, aliquam ut curabitur ipsum luctus ut rutrum. Odio condimentum donec suscipit molestie est etiam sit rutrum dui nostra, sem aliquet conubia nullam sollicitudin rhoncus venenatis vivamus rhoncus netus, risus tortor non mauris turpis eget integer nibh dolor. Commodo venenatis ut molestie semper adipiscing amet cras, class donec sapien malesuada auctor sapien arcu inceptos, aenean consequat metus litora mattis vivamus.</p>\n\n<pre>\n<code class="language-php">protected function getUserByRecaller($recaller)\n{\n	if ($this-&gt;validRecaller($recaller) &amp;&amp; ! $this-&gt;tokenRetrievalAttempted)\n	{\n		$this-&gt;tokenRetrievalAttempted = true;\n\n		list($id, $token) = explode("|", $recaller, 2);\n\n		$this-&gt;viaRemember = ! is_null($user = $this-&gt;provider-&gt;retrieveByToken($id, $token));\n\n		return $user;\n	}\n}</code></pre>\n\n<p>Feugiat arcu adipiscing mauris primis ante ullamcorper ad nisi, lobortis arcu per orci malesuada blandit metus tortor, urna turpis consectetur porttitor egestas sed eleifend. Eget tincidunt pharetra varius tincidunt morbi malesuada elementum mi torquent mollis, eu lobortis curae purus amet vivamus amet nulla torquent, nibh eu diam aliquam pretium donec aliquam tempus lacus. Tempus feugiat lectus cras non velit mollis sit et integer, egestas habitant auctor integer sem at nam massa himenaeos, netus vel dapibus nibh malesuada leo fusce tortor. Sociosqu semper facilisis semper class tempus faucibus tristique duis eros, cubilia quisque habitasse aliquam fringilla orci non vel, laoreet dolor enim justo facilisis neque accumsan in.</p>\n\n<p>Ad venenatis hac per dictumst nulla ligula donec, mollis massa porttitor ullamcorper risus eu platea, fringilla habitasse suscipit pellentesque donec est. Habitant vehicula tempor ultrices placerat sociosqu ultrices consectetur ullamcorper tincidunt quisque tellus, ante nostra euismod nec suspendisse sem curabitur elit malesuada lacus. Viverra sagittis sit ornare orci augue nullam adipiscing pulvinar libero aliquam vestibulum platea cursus pellentesque leo dui lectus, curabitur euismod ad erat curae non elit ultrices placerat netus metus feugiat non conubia fusce porttitor. Sociosqu diam commodo metus in himenaeos vitae aptent consequat luctus purus eleifend enim sollicitudin eleifend, porta malesuada ac class conubia condimentum mauris facilisis conubia quis scelerisque lacinia.</p>\n\n<p>Tempus nullam felis fusce ac potenti netus ornare semper molestie iaculis, fermentum ornare curabitur tincidunt imperdiet scelerisque imperdiet euismod. Scelerisque torquent curae rhoncus sollicitudin tortor placerat aptent hac, nec posuere suscipit sed tortor neque urna hendrerit, vehicula duis litora tristique congue nec auctor. Felis libero ornare habitasse nec elit felis, inceptos tellus inceptos cubilia quis mattis, faucibus sem non odio fringilla. Class aliquam metus ipsum lorem luctus pharetra dictum vehicula, tempus in venenatis gravida ut gravida proin orci, quis sed platea mi quisque hendrerit semper.</p>\n', 0, 1, 2),
(3, '2015-10-18 23:19:25', '2015-10-18 23:19:25', 'Post 3', 'post-3', '<img alt="" src="/filemanager/userfiles/user2/rouge-shell.png" style="float:left; height:128px; width:128px" /><p>\nLorem ipsum velit habitasse commodo maecenas ac ante integer pharetra blandit amet aliquam placerat enim luctus arcu, nec mattis conubia platea leo curabitur torquent libero molestie arcu litora vulputate tincidunt integer. \nDictumst dapibus habitant erat libero ligula aliquam faucibus sem gravida molestie, malesuada vulputate proin facilisis dictumst fusce sociosqu vivamus. \n</p>', '<p>\nLorem ipsum sollicitudin mauris lorem congue senectus quisque placerat leo, consectetur massa aliquam class varius ipsum tempus habitasse, donec tortor rhoncus nulla ipsum vestibulum condimentum vitae. \nUt cras pellentesque aliquam cubilia arcu nibh sed nunc justo urna taciti mi sollicitudin, amet lacinia commodo diam ultricies potenti ornare adipiscing ante justo ipsum sapien, porta lobortis elementum semper conubia posuere ante aliquam justo semper porta erat. \nCommodo purus sapien augue lacinia mattis pellentesque cubilia odio, sagittis sed libero dui sapien eros eget. \nMagna eget vel iaculis habitant massa hac molestie ut ad, nulla curabitur congue at porttitor purus class cubilia auctor feugiat, per neque curabitur facilisis amet cursus sed ut. \n</p>\n<p>\nEuismod nunc hac non est fringilla amet nec malesuada vestibulum mattis, integer porttitor per lacus elit amet justo luctus nullam urna, curabitur hendrerit curabitur varius proin sagittis nunc praesent ornare. \nConsequat dictumst sollicitudin quis vulputate iaculis duis accumsan consequat primis rhoncus, sollicitudin ullamcorper quam himenaeos porttitor duis inceptos sapien. \nSuspendisse porttitor mollis eu vulputate consectetur nec odio aptent purus neque, dolor mauris per vehicula rhoncus venenatis praesent felis consequat, etiam sapien fermentum platea ad vestibulum nunc proin in. \nTurpis dictum sed imperdiet integer nam dapibus, ad eu malesuada tellus in, habitant magna aptent eget vel. \n</p>\n<p>\nMaecenas ligula velit magna quis quam blandit litora sociosqu sodales, euismod tempus curabitur malesuada donec sem nisl varius. \nPretium torquent magna quis posuere litora platea fames praesent neque ligula, feugiat ultricies cras in curabitur dolor rhoncus vestibulum magna nibh, enim habitasse non quisque quis luctus non mollis praesent. \nLorem est aenean vel morbi varius commodo mi, libero donec et hac ultrices sociosqu, lobortis dolor varius primis ligula cras. \nIpsum tellus dictumst malesuada quis himenaeos luctus proin, maecenas sagittis nunc morbi habitasse proin eleifend, aliquam curabitur maecenas suscipit semper dapibus. \n</p>\n<p>\nDonec senectus rhoncus tellus aenean massa himenaeos aenean senectus velit, felis aenean pellentesque faucibus sodales elementum pretium aliquam et in, vitae tincidunt nunc venenatis aptent curae sollicitudin lectus. \nCubilia ac ullamcorper felis ac mauris ullamcorper arcu egestas lacus nulla, etiam per etiam fermentum tellus lacinia vel etiam sed blandit, fames hendrerit ultricies ut ac scelerisque urna justo felis. \nMagna sodales mattis sapien turpis aliquam facilisis aliquam, porttitor euismod quisque eros posuere eleifend curae elementum, id phasellus venenatis tempus massa lectus. \nUrna sit pellentesque metus fringilla nisi elit interdum metus, tristique quam nunc aliquet aptent iaculis at curabitur non, nunc sodales sagittis tristique lobortis posuere sodales. \n</p>\n<p>\nVelit vel odio augue ac sem eu quam habitasse ornare, condimentum quisque augue mi lobortis platea scelerisque mattis platea, ut urna ad leo habitant dictumst nunc et. \nAliquet lectus condimentum aliquam curabitur accumsan pretium phasellus turpis eu, primis aenean fusce ut cubilia dapibus integer suscipit venenatis pharetra, turpis malesuada himenaeos maecenas sagittis class donec suscipit. \nMagna sodales nec lacinia suspendisse quis blandit habitasse lectus aliquet bibendum, interdum lectus ultricies sem lacinia laoreet volutpat est fermentum justo, facilisis cras sociosqu proin euismod elit quisque imperdiet nostra. \nTaciti nibh imperdiet viverra vel a integer libero auctor molestie, praesent ac justo suspendisse viverra gravida erat praesent. \n</p>', 0, 1, 2),
(4, '2015-10-18 23:19:25', '2015-10-18 23:19:25', 'Post 4', 'post-4', '<img alt="" src="/filemanager/userfiles/user2/rouge-shyguy.png" style="float:left; height:128px; width:128px" /><p>\nLorem ipsum enim magna sollicitudin integer elit semper, non scelerisque tincidunt erat praesent feugiat convallis sociosqu, lorem iaculis consequat diam vitae blandit. \nPulvinar fermentum turpis eros magna fringilla vulputate nisl mollis fringilla, venenatis viverra iaculis molestie eros phasellus habitasse platea metus et, facilisis consectetur auctor libero mattis bibendum felis nunc. \n</p>', '<p>\nLorem ipsum porta bibendum sollicitudin dui auctor elit, euismod consectetur tempus etiam amet tristique habitasse, lobortis porta inceptos erat varius adipiscing. \nLaoreet eros velit diam sagittis rhoncus aenean vulputate, sociosqu ut morbi mauris nec porta, turpis mattis nulla duis curabitur metus. \nHabitasse curabitur metus curabitur praesent nisi gravida quis felis tempus, ante eget blandit ipsum lobortis molestie a semper morbi tristique, quisque orci hac rhoncus massa orci lorem metus. \nJusto rhoncus elementum netus posuere venenatis praesent ligula felis volutpat hendrerit, quisque nec adipiscing fusce sollicitudin elit non venenatis vulputate nibh, ornare orci nostra lacinia nullam torquent hac malesuada sociosqu. \n</p>\n<p>\nSagittis sociosqu urna velit nisi tellus hendrerit dictum, hac vulputate eget nam euismod elementum turpis, magna convallis tincidunt consectetur nam vivamus dictumst, senectus placerat facilisis elementum ultricies nostra. \nQuam ipsum nam ad rutrum luctus nullam cursus lacus adipiscing iaculis arcu ante cubilia, habitant nec dapibus fermentum lacus aenean dolor neque non tempus justo. \nOdio gravida dictumst proin donec aliquet lacinia etiam, lobortis luctus metus aliquam quisque cubilia nisl, varius quam nunc magna donec orci. \nImperdiet orci sociosqu molestie eget curabitur sociosqu risus laoreet curabitur condimentum mattis porta convallis, litora platea risus sed sit eu dictumst convallis felis in per in consequat, convallis potenti fermentum dapibus sem aliquet non suscipit dictum taciti dictumst dictum. \n</p>\n<p>\nVolutpat interdum sociosqu molestie fermentum inceptos ac pharetra dictumst nisl scelerisque, fusce quisque dictumst himenaeos lacinia lorem primis nec a nunc enim, cursus aptent varius nullam commodo et eros iaculis senectus. \nNullam tincidunt lobortis volutpat magna ultricies aliquam quisque torquent elementum, feugiat venenatis aenean himenaeos pulvinar porttitor at potenti at diam, luctus posuere vestibulum etiam eros dolor laoreet consequat. \nErat bibendum praesent sit primis vivamus morbi, viverra lacinia elit aliquet imperdiet. \nEnim maecenas platea tellus mollis eros consectetur nisi, lobortis phasellus aliquam cursus id urna ullamcorper, senectus semper metus ac ullamcorper vehicula. \n</p>\n<p>\nNam eleifend cras at donec magna adipiscing ultricies etiam nam lectus, porttitor aliquam sodales enim risus eleifend tincidunt mi mollis nostra suscipit, ligula sit volutpat donec odio platea praesent gravida commodo. \nSit convallis faucibus suspendisse euismod suspendisse nulla lorem duis sapien, taciti semper eros aliquam eleifend facilisis ornare rhoncus eleifend, placerat nunc morbi netus libero magna aliquam volutpat. \nPer justo blandit lacinia curabitur mattis ipsum tellus nibh, bibendum felis dictumst etiam adipiscing interdum posuere porta, taciti ante ullamcorper praesent lacinia massa etiam. \nMalesuada cursus iaculis ligula nec condimentum fusce pharetra rutrum hendrerit, posuere suscipit aenean sollicitudin taciti integer auctor at molestie torquent, bibendum sagittis eget amet nisi augue id pulvinar. \n</p>\n<p>\nDonec vehicula congue lacinia quisque sem habitasse nostra iaculis cras taciti sodales aptent, luctus litora urna luctus suscipit ultrices purus ac id ultrices metus. \nCongue augue ut integer porta quis sodales ad, libero iaculis eleifend mollis lacus auctor feugiat eros, semper praesent risus proin duis vivamus. \nHendrerit potenti morbi dictumst semper scelerisque suspendisse torquent mi, quisque fusce arcu ipsum sapien taciti cursus at rhoncus, ligula cursus interdum suscipit lacus placerat cursus. \nDictum lorem mi sit rhoncus ornare accumsan venenatis tincidunt, inceptos lacus quam varius arcu etiam. \n</p>', 0, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `post_tag`
--

CREATE TABLE IF NOT EXISTS `post_tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `post_tag_post_id_foreign` (`post_id`),
  KEY `post_tag_tag_id_foreign` (`tag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

--
-- Dumping data for table `post_tag`
--

INSERT INTO `post_tag` (`id`, `post_id`, `tag_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 1),
(4, 2, 2),
(5, 2, 3),
(6, 3, 1),
(7, 3, 2),
(8, 3, 4);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `title`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin', '2015-10-18 23:19:24', '2015-10-18 23:19:24'),
(2, 'Redactor', 'redac', '2015-10-18 23:19:24', '2015-10-18 23:19:24'),
(3, 'User', 'user', '2015-10-18 23:19:24', '2015-10-18 23:19:24');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tag` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tags_tag_unique` (`tag`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `created_at`, `updated_at`, `tag`) VALUES
(1, '2015-10-18 23:19:25', '2015-10-18 23:19:25', 'Tag1'),
(2, '2015-10-18 23:19:25', '2015-10-18 23:19:25', 'Tag2'),
(3, '2015-10-18 23:19:25', '2015-10-18 23:19:25', 'Tag3'),
(4, '2015-10-18 23:19:25', '2015-10-18 23:19:25', 'Tag4');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  `seen` tinyint(1) NOT NULL DEFAULT '0',
  `valid` tinyint(1) NOT NULL DEFAULT '0',
  `confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `confirmation_code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_id_foreign` (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=15 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role_id`, `seen`, `valid`, `confirmed`, `confirmation_code`, `created_at`, `updated_at`, `remember_token`) VALUES
(1, 'GreatAdmin', 'admin@la.fr', '$2y$10$T4.Xkcdu4p3GMiwFOXjqCuw9dm0wzU485Da6TfS3OZxh6jgPViNpS', 1, 1, 0, 1, NULL, '2015-10-18 23:19:24', '2015-10-19 01:30:06', 'i3xMjgYbbvK8T6p6wTItcOxxGdulHCHQEB0v0UrfljhpJ9clkmHW3UqKZl2u'),
(2, 'GreatRedactor', 'redac@la.fr', '$2y$10$w7Kq.QspB6ohLCIW.kPr0.BzOPY4MMbe1Rbolrj9kcc8ZhnQq66hG', 2, 1, 1, 1, NULL, '2015-10-18 23:19:24', '2015-10-18 23:19:24', NULL),
(3, 'Walker', 'walker@la.fr', '$2y$10$2BKnzB9oUG2/26yFGcPkdemd2LAYmaJYdY5jJX9pVFH63p/ROys4.', 3, 0, 0, 0, NULL, '2015-10-18 23:19:24', '2015-10-19 01:29:44', NULL),
(4, 'Slacker', 'slacker@la.fr', '$2y$10$nNOan08WQLnbeNC9aHc94OqkJKo.WsPNYTXZLqlYGvpgDGs8n4xPK', 3, 0, 0, 1, NULL, '2015-10-18 23:19:24', '2015-10-18 23:19:24', NULL),
(14, 'daotran', 'dao.tran@enclave.vn', '$2y$10$rWcLecijpQGxaOu0DkT/weVg9hHrq2pei8k7zpnvGpesSynKB.Aki', 2, 1, 0, 0, NULL, '2015-10-19 01:29:24', '2015-10-19 01:29:34', NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `post_tag`
--
ALTER TABLE `post_tag`
  ADD CONSTRAINT `post_tag_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`),
  ADD CONSTRAINT `post_tag_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
