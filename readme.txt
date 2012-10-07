=== Plugin Name ===
Contributors: BenjaminSommer
Donate link: http://academicpress.benjaminsommer.com/
Tags: academic, academia, further reading, references, bibliography, table of bibliographies, table of footnotes, citation, footnotes, MLA, APA, Chicago, Turabian, Harvard, bibtex, ris, endnote, export, import
Requires at least: 2.8
Tested up to: 3.4.2
Stable tag: 1.1.1
License: CC GNU GPL 2.0 license

Turn your Blog into an academic publishing site

== Description ==

**Manage Bibliographic Citations**

Academic Press supports Apa, Chicago, Harvard, MLA, Turabian citation styles to automatically format your references. When doing inline citations,
either use automatic formatting or contextual formatting so that your citation perfectly fits into your sentence. A simple shortcode is perfectly
fine to display the table of bibliography below your posts.


**Import/Export Popular Bibliographic Formats**

Use BibTex, Endnote, Medline, RIS or other popular formats to display citations on your posts. 
Use the powerful scripting engine to import data from these formats, working with these data sets (search, sort, extract, merge) using 
collections, and finally to export data from a collection to other formats.


**Footnotes**

Dynamically hide additional content to improve the flow of your articles. Custom numbering formats include numeric, alphabetic (lower and upper case), 
roman (lower and upper case), and greek (lower and upper case). Table of footnotes is automatically displayed at the end of each post, supporting
several options.


**Fast and Lightweight Plugin**

Academic Press has been designed to be lightweight (use of a clean modern programming convention).
It does not create additional database tables: all data are stored in WordPress posts by using shortcodes, or in external resources (optional).
This plugin comes with a "VirtualBox" to test your shortcodes on the fly while writing articles, thus you can easily check correct citations. 
You can use this VirtualBox to easily convert between BibTex, Endnote or other formats.


**Supports Internationalization**

* English
* German


**Future Features**
A data stream to bibliographic servers to more easily search for references, or to store the results (i.e. collections) on 
external resources (e.g. bibliographic servers). And much more languages. And custom citation styles. 


== Installation ==

= Software Requirements =
1. PHP 5.2 (no additional libraries required)

= Install =
1. Download and install from within WordPress
1. Choose: Activate or Network Activate
1. Configure AcademicPress

= Notice =
Use of this plugin assumes acceptance of this plugin's license and its Terms of Use, to be found in license.txt.

== Frequently Asked Questions ==

= How to cite a reference? =

The AcademicPress VirtualBox lists all major commands. You can find it when editing posts.

= How to automatically append table of bibliography? =

Go to Settings > AcademicPress, and enter the following command into a script postprocessor textarea for a posttype of your choice: `[acp display title="Table of Bibliography" /]`.

= I have more questions! =

Please read the tutorials: http://academicpress.benjaminsommer.com/tutorials/

== Screenshots ==

VirtualBox: http://academicpress.benjaminsommer.com/files/2012/09/Screenshot-from-2012-09-24-094943.png

== Changelog ==

= 1.1.1 =
* Fixed table of bibliography: previously, citations of all other posts had been displayed when using post lists
* Improved settings page: use of tabs for better organisation

= 1.1 =
* AcademicPress now comes with new default, built-in bibliographic style (acp), an optimized version for Web users.
* Updated Bibliographic Styles: Harvard, Turabian.
* All bibliographic styles are content-aware: citations are always nicely formatted, even if attributes are missing.

= 1.0 =
* Added Pre- and Postprocessors
* Added a VirtualBox
* Added a Scripting Engine to work with `acp`-shortcodes
* Branch from WordPress Plugin `Netblog` (citation styles only)