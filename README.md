Basic progress notes for future documentation:
---------------------
**excelUpload.php:**  
Imports xlsx file  

TODO:  
- [ ] Test with different kind of cell formats, borders, empty cells, wrong columns.  
---------------------
**readExcel.php:**  
Lists all names imported from excel  

TODO:  
- [ ] Display the names in a nice format.  
- [ ] Allow user to edit name/email before searching. If user changes the value, update DB.  
---------------------
**collectMdxPapers.php:**  
get first name and last name  
search for ALL ePrint publications (can't filter by date at this point)  
Shows how many publication found for this specific author.  
If title is null, or date is < 2014, ignores paper. Doesn't save anywhere.  
Automatically assign ERA grade according to ISSN. ---- **is there any better way to check it?**  
Check if publication has at least one author. Proceed if yes.  
Check if author's email ends with @mdx.ac.uk.  
If yes, search by email OR full name.  
if no, only searches by full name.  
IF found    : updates first name/last name + email. Useful if found by name, but doesn't have email saved, and vice-versa  
IF NOT found: insert all details, marking as current employee if email ends with @mdx.ac.uk --- **not 100% guaranteed**.  
Check if publication+author pair is already in the DB. Insert if not.

TODO:  
- [ ] show how many publication found, how many since 2014, how many already in the DB, how many added.  
- [ ] list all the publications from previous point with Collapsibles/Accordions.  
- [ ] if no publications found for specific authors (using name/surname), ask user to provide authors email. If still nothing found, suggest similar names with high similarity. If found, ask user permission to update name/email values in the DB.  

---------------------
**fullList.php:**  
Lists all publications (id - with link to ePrint, title, abstract, date, ERA, isPub, PresType, publication, publisher) and authors (full name and email)  
Check if pair publication+author has already been assigned to REF, if YES, automatically selects it from the dropbox, if not, shows the default option "No REF Assigned"  
User can also change the dropdown to either remove the REF assigned by selecting the first option, assign it to a new one, or simply assign to a different one.
Hover the abstract line for it to expand.  

TODO:  
- [x] Show what REF the pair (publication+author) is assigned to. If none, show option add to it.  
- [x] Assign REF when changing the dropdown value next to authors name.  
- [ ] Allow user to manually update any field. Double check before submitting change.  

---------------------
**updateRef.php**  
Receives the POST request from fullList.php to CRUD the refUnits.  

---------------------
**publicationsPerAuthor.php:**  
Lists author (name/email), if current employee, how many publications saved in the systemm (since 2014), option to edit/delete it.  
When clicking on the # of publications link, it pop-up a window with list of the publication titles and dates, related to this user.  
It send POSTS requests via **getAuthorPubs.php** file to get the list above.  

TODO:  
- [ ] Improve overall layout. Use collapsibles/accordions instead of pop-up.  
- [ ] Maybe show more details about the published papers? Name of other authors?  

---------------------
**getAuthorPubs.php**  
Receives the POST request from totalOfPublishedPub.php to get all published papers by specific author, since 2014.  

---------------------
**refUnits.php**  
List all ref units and the papers assigned to it.  

---------------------
**Others:**
- [ ] Page where it shows all authors, show a section of similar names and ask user if they want to merge it.  
- [ ] Allow publications/authors to be searched/added manually.  
