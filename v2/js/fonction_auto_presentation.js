//---------------------------------------------------------------------
// test_email_valide 
//---------------------------------------------------------------------
// Test la validité de saisie d'une adresse email
//---------------------------------------------------------------------
//Parametres d'entrés :
//		- mail => notre champs email à tester
//Parametres de sortis :
//		- si email valide retourne TRUE sinon FALSE
//------------------------------------------------------------------------
function test_email_valide(mail)
{
	var expression=new RegExp("^[a-z0-9._-]+@[a-z0-9.-]{2,}[.][a-z]{2,4}$","i");
	if(expression.test(mail))
	{
		return true;
	}
	else
	{
		return false;
	}
}
//---------------------------------------------------------------------
// Fin test_email_valide 
//---------------------------------------------------------------------

//---------------------------------------------------------------------
// test_date_valide 
//---------------------------------------------------------------------
// Test la validité de saisie d'une adresse date
//---------------------------------------------------------------------
//Parametres d'entrés :
//		- param_date => notre champs date à tester format JJ/MM/AAAA
//Parametres de sortis :
//		- si date valide retourne TRUE sinon FALSE
//------------------------------------------------------------------------
function test_date_valide(param_date)
{
	var expression=new RegExp("([0-9]{2,2})(/)([0-9]{2,2})(/)([0-9]{4,4})");
	if(expression.test(param_date))
	{
		return true;
	}
	else
	{
		return false;
	}
}
//---------------------------------------------------------------------
// fin test_date_valide 
//---------------------------------------------------------------------

//---------------------------------------------------------------------
// test_champ_vide 
//---------------------------------------------------------------------
// Test si un champ est vide
//---------------------------------------------------------------------
//Parametres d'entrés :
//		- txtChamp => notre champs texte à tester
//Parametres de sortis :
//		- si champ vide retourne TRUE sinon FALSE
//------------------------------------------------------------------------
function test_champ_vide(txtChamp)
{
	if(txtChamp=="")
	{
		return false;
	}
	else
	{
		return true;
	}
}
//---------------------------------------------------------------------
// fin test_champ_vide 
//---------------------------------------------------------------------
//---------------------------------------------------------------------
// limit_check 
//---------------------------------------------------------------------
// limite le nombre de check box cochable
//---------------------------------------------------------------------
//Parametres d'entrés :
//		- nom_champ => nom de notre checkbox
//		- nbr_limit => nombre limitant
//		- id_form	=> id de notre formulaire
//------------------------------------------------------------------------
function limite_check(nom_champ, nbr_limit, id_form) 
{
	var nbr = 0;
	var nbr_check = 0;

	nom = document.getElementById(id_form).elements[nom_champ];
	//alert(nom);
	nbr_check = nom.length;

	for(i = 0; i < nbr_check; i++)
	{
		if(nom[i].checked == true)
		{
			nbr++;
		}
	}

	if(nbr >= nbr_limit) 
	{
		for(i = 0; i < nbr_check; i++) 
		{
			if(nom[i].checked == false)
			{
				nom[i].disabled = true;
			}
		}
	}
	else
	{
		for(i = 0; i < nbr_check; i++) 
		{
			if(nom[i].checked == false)
			{
				nom[i].disabled = false;
			}
		}
	}
}
//---------------------------------------------------------------------
// fin limit_check 
//---------------------------------------------------------------------