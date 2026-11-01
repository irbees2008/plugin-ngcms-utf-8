/**
 * SyntaxHighlighter brush for Windows Command Prompt and batch files.
 * Compatible with SyntaxHighlighter 3.0.83.
 */
;(function()
{
	typeof(require) != 'undefined' ? SyntaxHighlighter = require('shCore').SyntaxHighlighter : null;

	function Brush()
	{
		var keywords = 'if else exist defined errorlevel in do for goto call setlocal endlocal shift not echo off on enabledelayedexpansion enableextensions title prompt pause exit choice start pushd popd assoc ftype break rem';
		var commands = 'arp assoc attrib bcdedit cacls call cd chcp chdir chkdsk chkntfs choice cipher clip cls cmd color comp compact convert copy cscript date del dir diskpart doskey driverquery echo endlocal erase eventcreate exit expand fc find findstr for format fsutil ftype fver help hostname icacls if ipconfig label md mkdir mklink mode more move net netstat nslookup path pause ping popd powercfg print prompt pushd rd recover ren rename replace rmdir robocopy route runas sc schtasks set setlocal sfc shift shutdown sort start subst systeminfo taskkill tasklist time timeout title tree type ver verify vol where whoami wmic xcopy';

		this.regexList = [
			{ regex: /^\s*(?:rem\b.*|::.*)$/gim, css: 'comments' },
			{ regex: SyntaxHighlighter.regexLib.doubleQuotedString, css: 'string' },
			{ regex: /%%?[~$a-z0-9_:-]*[a-z0-9_]|![a-z0-9_]+!/gim, css: 'variable' },
			{ regex: /\b(?:%[0-9*]|%%?[a-z])\b/gim, css: 'variable' },
			{ regex: new RegExp(this.getKeywords(keywords), 'gim'), css: 'keyword' },
			{ regex: new RegExp(this.getKeywords(commands), 'gim'), css: 'functions' }
		];
	}

	Brush.prototype = new SyntaxHighlighter.Highlighter();
	Brush.aliases = ['cmd', 'batch', 'bat', 'dos'];
	SyntaxHighlighter.brushes.Cmd = Brush;

	typeof(exports) != 'undefined' ? exports.Brush = Brush : null;
})();
