NAME
	
	speller - host your own spell checker

VERSION
	
	version 0.1
	
DESCRIPTION
	
	Host your own spell checker (running MySQL)
	
	I was thinking quite some time to create my self-hosted
	spell checker but had bumped to performance issues since PHP
	was not THAT fast when it came down to a big dictionary.
	
	So, I thought to create a MySQL UDF implementation of the
	levenshtein function and create a spell checker.
	
	Thankfully I found that sam j levy [ samjlevy.com ] had
	already done that. So what I did here was simply to show
	you how to install and implement your own spell checker.
	
	In other words, I did nothing.
	
INSTALLATION
	
	First you need to install the levenshtein plugin to MySQL.
	I have also included a compiled plugin for CentOS 6 x64 but
	if you need to recompile it here is how:
	
	yum install gcc-c++ mysql++-devel
	g++ -fPIC -I /usr/include/mysql/ -o levenshtein.so -shared levenshtein.cc
	cp levenshtein.so /usr/lib64/mysql/plugin/
	
	For Windows: copy levenshtein.dll into /lib/plugin
	For Linux: copy levenshtein.so into /lib/mysql/plugin
	
	After copying the file you need to create the function in MySQL.
	Login as root and for windows execute:
	
	CREATE FUNCTION levenshtein RETURNS INTEGER SONAME 'levenshtein.dll';
	
	and for linux:
	
	CREATE FUNCTION levenshtein RETURNS INTEGER SONAME 'levenshtein.so';
	
	That's it, no MySQL restart is required. Everything should work.
	
TESTING

	Upload speller.php to your server and import speller.sql file into MySQL.
	
	Use it like this:
	
		speller.php?word=hellp
	
	It will return a JSON reply:
	
	{
		"success":true,
		"correct":false,
		"message":"",
		"suggest":["hell","hello","help"]
	}
	
	success: If false, "message" will contain the error.
	correct: If the input word is spelled correctly, this will be set to true.
	message: error message (if there is one)
	suggest: array with possible suggestions (sorted)
	
BENCHMARK

	With a dictionary of 250.000 words. Testing with 'helo'
	
	PHP Implementation: 	0.86782
	MySQL Implementation:	0.29684
	
	Quite a difference.
	
AUTHOR
	
	Pavel Tsakalidis [ p@vel.gr ] [ http://pavel.gr ]
	
CONTRIBUTORS
	
	sam j levy [ samjlevy.com ]
	levenshtein UDF implementation: http://samjlevy.com/2011/03/mysql-levenshtein-and-damerau-levenshtein-udfs/
	
	English Dictionary from: https://addons.mozilla.org/en-US/firefox/language-tools/

COPYRIGHT AND LICENSE
	
	Licensed under the MIT license: http://www.opensource.org/licenses/MIT