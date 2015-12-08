/*	

Деление числа на разряды

Введите пятизначное число: 10819

1 цифра равна 1
2 цифра равна 0
3 цифра равна 8
4 цифра равна 1
5 цифра равна 9

*/

#include "ParseNum.h";

void ParseNum::parse()
{
	string num;
	
	logMessage();
	cin >> num;
	while(num.size()!=5)
	{
		cout << "Ошибка: Число не пятизначное" << endl;
		logMessage();
		cin >> num;
	}

	for(size_t i = 1;i<6;++i)
	{
		cout << i << " цифра равна "<< num[i-1] << endl;
	}

};

void ParseNum::logMessage()
{
	cout << "Введите пятизначное число:" << endl;
}


/*
			Прямоугольный треугольник в С++

*
**
***
****
*****
******
*******

*/

void DrawTriangle::drawA()
{
	int len = 7;
	for(int i=0;i<len;++i)
	{
		for(int j=0;j <= i;++j)
		{
			cout << "*";
		}
		cout << endl;
	}
}

void DrawTriangle::drawB()
{
	int len = 7;
	for(int i=0;i<len;++i)
	{
		string foo =  string(i+1,'*');
		cout << foo <<endl;
	}
}


/* Напишите программу, запрашивающую имя, фамилия, отчество и номер группы студента и выводящую введённые данные в следующем виде:
			   ********************************
			   * Лабораторная работа № 1      *
			   * Выполнил(а): ст. гр. ЗИ-123  *
			   * Иванов Андрей Петрович       *
			   ******************************** 
Необходимо, чтобы программа сама определяла нужную длину рамки. Сама же длинна рамки зависит от длинны наибольшей строки внутри рамки. Используя циклы for легко можно выровнять стороны рамки.*/

FrameDraw::FrameDraw(): longest_str(0),lab_str("Лабораторная работа № 1"),done_str("Выполнил(а): ст. гр."),tabs_str("\t\t"){ setlocale(LC_ALL,"");};

void FrameDraw::handleMessage(std::string student_prop,std::string& val)
{
	cout << "Введите " << student_prop << " студента:";
	cin>>val;
	cout << endl;
}

void FrameDraw::addPadding(std::string& checked)
{
	int num_pads = longest_str - checked.size();
	checked = " " + checked  + std::string(num_pads,' ') + " ";
}

void FrameDraw::updateLongest(std::string& checked)
{
	if(longest_str < checked.size() ) longest_str = checked.size();
}

void FrameDraw::updateLongestStringSize()
{
	updateLongest(lab_str);
	updateLongest(done_str);
	updateLongest(full_name);
}

void FrameDraw::padStrings()
{
	addPadding(lab_str);
	addPadding(done_str);
	addPadding(full_name);
}

void FrameDraw::handleAllMessages()
{
	handleMessage("имя",name);
	handleMessage("фамилию",surname);
	handleMessage("отчество",second_name);
	handleMessage("номер группы",group);
}

void FrameDraw::logFramed(std::string& val)
{
	cout<<'*'<<val<<'*'<<endl;
}

void FrameDraw::init()
{
	handleAllMessages();

	done_str = done_str + " " + group;
	full_name = name + " " + surname + " " + second_name;

	updateLongestStringSize();
	padStrings();
	horizontal_border_frame = std::string(longest_str+2,'*');
	
	logFramed(horizontal_border_frame);
	logFramed(lab_str);
	logFramed(done_str);
	logFramed(full_name);
	logFramed(horizontal_border_frame);
}