#ifndef ARRAYS_AND_STRINGS_H
#define ARRAYS_AND_STRINGS_H

#include <iostream>
#include <string>
#include <map>
/*
Implement an algorithm to determine if a string has all unique characters. What
if you cannot use additional data structures?
*/

class AllUniqueChars
{
public:
	AllUniqueChars(){};
	bool strUnique(std::string& str);	
};

/*
Implement a function void reverse(char* str) in C or C++ which reverses a nullterminated string
*/
class ReverseCStr
{
public:
	ReverseCStr(){}
	void reverse(char *arr);
};

/*
Given two strings, write a method to decide if one is a permutation of the other.
*/
class PermCheck
{
public: 
	PermCheck(){};
	bool isPermutaion(std::string& a,std::string& b);
	std::string& sort(std::string& str);
};


/*
Write a method to replace all spaces in a string with'%20'. You may assume that
the string has sufficient space at the end of the string to hold the additional
characters, and that you are given the "true" length of the string. (Note: if implementing in Java, please use a character array so that you can perform this operation in place.)
EXAMPLE
Input: "Mr John Smith
Output: "Mr%20Dohn%20Smith"
*/

class Rewrite20
{
public:
	std::string input_line;
	Rewrite20(){};
	bool isSpace(char s);
	void getInput();

};


/*
Implement a method to perform basic string compression using the counts
of repeated characters. For example, the string aabcccccaa a would become
a2blc5a3. If the "compressed" string would not become smaller than the original string, your method should return the original string
*/
class StrCompressor
{
public:
	StrCompressor(){};
	std::string&  compress(std::string& s);
};

/*
Given an image represented by an NxN matrix, where each pixel in the image is
4 bytes, write a method to rotate the image by 90 degrees. Can you do this in
place?

!~~ what a fun excercise!

*/
template <size_t N>
class Rot90
{
public:
	Rot90(){};
	void rotate(int (&arr)[N][N]);
	void logArr(int (&arr)[N][N]);
private:
	int getWidth(int n);
};

template <size_t N>
void Rot90<N>::rotate(int (&arr)[N][N])
{
	int offset = 0;
	int last = N-1;
	for(int i=0;i<N/2;++i)
	{
		for(int j=offset;j<last-offset;++j)
		{		
			int temp = arr[i][j]; //save top
			arr[i][j] = arr[j][last-offset]; // top = right
			arr[j][last-offset] = arr[last-offset][last-j]; // right = bottom
			arr[last-offset][last-j] = arr[last-j][offset]; // bottom = left
			arr[last-j][offset] = temp; // left = top 
		}
		++offset;
	}
}

template <size_t N>
int Rot90<N>::getWidth(int n)
{
	int width = 0;
	int base = 10;
	do 
	{
		++width; 
		n /= base;
	} 
	while (n);
	return width;
}



template <size_t N>
void Rot90<N>::logArr(int (&arr)[N][N])
{
	int max_width;
	int max_digit=0;
	for(int i=0;i<N;++i)
	{
		for(int j=0;j<N;++j)
		{
			if(max_digit < arr[i][j]) 
				max_digit = arr[i][j];
		}
	}

	max_width = getWidth(max_digit);

	for(int i=0;i<N;++i)
	{
		for(int j=0;j<N;++j)
		{
			int pad = max_width - getWidth(arr[i][j]);
			std::cout<< std::string(pad,' ')<<arr[i][j]<<' ';
		}
		std::cout<<std::endl;
	}

	std::cout<<std::endl;
}


/*
Write an algorithm such that if an element in an MxN matrix is 0, its entire row
and column are set to 0.

*/

template <size_t M,size_t N>
class ZeroRower
{
public:
	ZeroRower(){};
	void setZeroRowsCols(int (&arr)[M][N]);
	void setZeroRow(int (&arr)[M][N],int j);
	void setZeroCol(int (&arr)[M][N],int i);
	void logArr(int (&arr)[M][N]);
private:
	int getWidth(int n);
};

template <size_t M,size_t N>
void ZeroRower<M,N>::setZeroRow(int (&arr)[M][N],int i)
{
	for(int j=0;j<N;++j)
	{
		arr[i][j] = 0;
	}
}

template <size_t M,size_t N>
void ZeroRower<M,N>::setZeroCol(int (&arr)[M][N],int j)
{
	for(int i=0;i<M;++i)
	{
		arr[i][j] = 0;
	}
}

template <size_t M,size_t N>
void ZeroRower<M,N>::setZeroRowsCols(int (&arr)[M][N])
{
	int rows[M] = {0};
	int cols[N] = {0};
	for(int i=0;i<M;++i)
	{
		if(rows[i])continue;
		for(int j=0;j<N;++j)
		{

			if(rows[j])continue;
			if(arr[i][j]==0 && !rows[i] && !cols[j])
			{				
				rows[i] = 1;
				cols[j] = 1;
				setZeroRow(arr,i);
				setZeroCol(arr,j);
			}
		}
	}

}

template <size_t M,size_t N>
void ZeroRower<M,N>::logArr(int (&arr)[M][N])
{
	int max_width;
	int max_digit=0;
	for(int i=0;i<M;++i)
	{
		for(int j=0;j<N;++j)
		{
			if(max_digit < arr[i][j]) 
				max_digit = arr[i][j];
		}
	}

	max_width = getWidth(max_digit);

	for(int i=0;i<M;++i)
	{
		for(int j=0;j<N;++j)
		{
			int pad = max_width - getWidth(arr[i][j]);
			std::cout<< std::string(pad,' ')<<arr[i][j]<<' ';
		}
		std::cout<<std::endl;
	}

	std::cout<<std::endl;
}


template <size_t M,size_t N>
int ZeroRower<M,N>::getWidth(int n)
{
	int width = 0;
	int base = 10;
	do 
	{
		++width; 
		n /= base;
	} 
	while (n);
	return width;
}


#endif