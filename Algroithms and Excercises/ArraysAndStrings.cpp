#include "ArraysAndStrings.h"
/*
Implement an algorithm to determine if a string has all unique characters. What
if you cannot use additional data structures?
*/


bool AllUniqueChars::strUnique(std::string& str)
{
	
	for(size_t i=0;i<str.size()-1;++i)
	{
		int repeat_count = 0;
		for(size_t j= 0;j<str.size();++j)
		{
			if(str[i]==str[j]) ++repeat_count;
			if(repeat_count > 1) return false;
		}
	}
	return true;
};


/*
Implement a function void reverse(char* str) in C or C++ which reverses a nullterminated string
*/

void ReverseCStr::reverse(char* arr)
{

	 if( arr == NULL || !(*arr) )
		return;

	int len = strlen(arr);
	char temp;
	for(size_t i = 0;i<len/2;++i)
	{
		temp = arr[i];
		arr[i] = arr[len-i-1];
		arr[len-i-1] = temp;
	}

}

/*
Given two strings, write a method to decide if one is a permutation of the other.
*/

bool PermCheck::isPermutaion(std::string& a, std::string& b)
{
	if( a.size() != b.size() ) return false;
	return sort(a) == sort(b);
}

std::string& PermCheck::sort(std::string& str)
{
	for(std::size_t i=0;i<str.size()-1;++i)
	{
		for(std::size_t j=i+1;j<str.size();++j)
		{
			if(str[i]>str[j])
			{
				//std::swap(str[i],str[j]);
				char temp = str[i];
				str[i]=str[j];
				str[j]=temp;
			}
		}
	}
	return str;
}

/*
Write a method to replace all spaces in a string with'%20'. You may assume that
the string has sufficient space at the end of the string to hold the additional
characters, and that you are given the "true" length of the string. (Note: if implementing in Java, please use a character array so that you can perform this operation in place.)
EXAMPLE
Input: "Mr John Smith
Output: "Mr%20Dohn%20Smith"
*/

bool Rewrite20::isSpace(char s)
{
	return s == ' ';
}

void Rewrite20::getInput()
{
	std::cout<<"Enter input: ";
	std::getline(std::cin,input_line);
	std::cout<<std::endl;

	std::string output;

	for(size_t i=0;i<input_line.size();++i)
	{
		if(isSpace(input_line[i])) output+="%20";
		else output+=input_line[i];
	}
	std::cout<<output<<std::endl;
}

/*
Implement a method to perform basic string compression using the counts
of repeated characters. For example, the string aabcccccaa a would become
a2blc5a3. If the "compressed" string would not become smaller than the original string, your method should return the original string
*/


std::string& StrCompressor::compress(std::string& s)
{
	char prev = s[0];
	std::string comp;
	size_t counter = 1;
	for(size_t i=1;i<s.size()+1;++i)
	{

		if(s[i]==prev)
			++counter;
		else
		{
			comp = counter == 1 ? comp + prev :  comp + prev + std::to_string((_ULonglong)counter);
			counter=1;
		}
		prev = s[i];
	}
	std::swap(s,comp);
	return s;
}


/*
Given an image represented by an NxN matrix, where each pixel in the image is
4 bytes, write a method to rotate the image by 90 degrees. Can you do this in
place?

!~~ Implemented in templates via Rot90 class

*/


/*
Write an algorithm such that if an element in an MxN matrix is 0, its entire row
and column are set to 0.

!~~ Implemented in templates via ZeroRower class

*/