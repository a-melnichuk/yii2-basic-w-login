#ifndef PARSE_NUM_H
#define PARSE_NUM_H

#include <iostream>
#include <string>
#include <locale>
#include <cstdlib> // для system
#include <string>
#include <Windows.h>
using namespace std;

class ParseNum
{
public:
	ParseNum(){};
	void parse();
private:
	void logMessage();
};

class DrawTriangle
{
public:
	DrawTriangle(){};
	void drawA();
	void drawB();
};


class FrameDraw
{
	size_t longest_str;
	std::string lab_str,done_str,tabs_str,
	name,surname,second_name,group,full_name,horizontal_border_frame;
public:
	FrameDraw();
	void addPadding(std::string& checked);
	void init();
private:
	void handleMessage(std::string student_prop_name,std::string& val);
	void handleAllMessages();
	void updateLongest(std::string& checked);
	void updateLongestStringSize();
	void padStrings();
	void logFramed(std::string& val);
};

#endif