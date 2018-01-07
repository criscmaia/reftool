<style>
    #publications {
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #publications td,
    #publications th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #publications tr:hover {
        background-color: #ddd;
    }

    #publications th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #4CAF50;
        color: white;
    }

    .ellipse {
        width: 400px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        margin: 0;
        padding: 0;
    }

    .ellipse:hover {
        padding: 2px;
        white-space: normal;
        word-break: break-word;
        z-index: 5;
    }

</style>
<table id="publications">
    <thead>
        <tr style="text-align: left">
            <th>Publication details</th>
            <th>Date</th>
            <th>ERA</th>
            <th>isPub presType</th>
            <th>More details</th>
            <th>Authors</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td rowspan="4">
                <a href="#">14039</a> - An approach to early evaluation of informational privacy requirements
                <ul>
                    <li class="ellipse"><strong>Abstract: </strong>The widespread availability of information in the digital age places a significant demand on the privacy needs of individuals. However, privacy considerations in requirements management are often treated as non-functional concerns and in particular, early feedback of privacy concerns is not easily embedded into current requirements practice. Luciano Floridi's Ontological Theory of Informational Privacy presents an extensive interpretation of informational privacy using concepts such as ontological friction. This paper first re-casts the theory in terms of modelling constructs and then applies the theory in the form of a Bayesian network of beliefs in the context of an existing research project aimed at developing socio-technical system delivered as a mobile app in the UK youth justice system. The operationalisation of the theory and its relationship to value sensitive design creates opportunities for early evaluation of informational privacy concerns in the requirements process.</li>
                </ul>
            </td>
            <td rowspan="4">
                2015-04-13
            </td>
            <td rowspan="4">

            </td>
            <td rowspan="4">
                pub <br> paper
            </td>
            <td rowspan="4">
                <br> Association for Computing Machinery (ACM) <br> 30th ACM Symposium on Applied Computing 2015 <br>
            </td>
        </tr>
        <tr>
            <td>Balbir Barn<br>B.Barn@mdx.ac.uk</td>
        </tr>
        <tr>
            <td>Giuseppe Primiero<br>G.Primiero@mdx.ac.uk</td>
        </tr>
        <tr>
            <td>Ravinder Barn<br>r.barn@rhul.ac.uk</td>
        </tr>
    </tbody>
</table>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'dbconnect.php';
?>
