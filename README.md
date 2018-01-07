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

---------------------
