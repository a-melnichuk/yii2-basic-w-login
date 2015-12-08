#ifndef G_STACK_H
#define G_STACK_H
#include <iostream>
#include <stdexcept>
#include <math.h>
#include <cstdio>
template <typename T>
class Stack { //LIFO

public:
    class Node {
        friend class Stack<T>;
		//friend class SetOfStacks<T>;

    public:
        Node(T d, Node* n = NULL) : data(d), next(n) {}
		void log(){	std::cout<<data<<std::endl; }
		T data;
        Node* next;
	};

    Node *head,*tail;  // End of list
    int size;    // Number of nodes in list

public:
	static void hanoi(int len);
    Stack(const Stack<T>& src);  // Copy constructor
    ~Stack();  // Destructor

    Stack() : head(NULL), tail(NULL), size(0) {}
    Stack<T>& push(T d);   // Insert element at end
    Stack<T>& pop();  // Remove element from end
	Stack<int>& sort();
	T& peek();
	Node* findByIndex(int i);
    void dump();  // Output contents of list
	bool is_empty();
};

// Copy constructor
template <typename T>
Stack<T>::Stack(const Stack<T>& src) :
        head(NULL), tail(NULL), size(0) {

    Node* current = src.head;
    while (current != NULL) 
	{
        this->push(current->data);
        current = current->next;
    }

}

// Destructor
template <typename T>
Stack<T>::~Stack() 
{
    while (size!=0) 
		this->pop();   
}

 

// Insert an element at the end
template <typename T>
Stack<T>& Stack<T>::push(T d) {

    Node* new_tail = new Node(d, NULL);

    if (size==0) 
        head = new_tail;
     else 
        tail->next = new_tail;

    tail = new_tail;
    ++size;
	return *this;
}

template <typename T>
T& Stack<T>::peek() 
{
  return tail->data;
}

template <typename T>
bool Stack<T>::is_empty() 
{
  return size == 0;
}

template <typename T>
Stack<T>& Stack<T>::pop() 
{
    Node* old_tail = tail;

    if (size== 1) 
	{
        head = NULL;
        tail = NULL;
    } 
	else 
	{
        // Traverse the list to node just before tail
        Node* current = head;
        while (current->next != tail) 
            current = current->next;

        // Unlink and reposition
        current->next = NULL;
        tail = current;
    }

    delete old_tail;
    --size;
	return *this;
}

// Display the contents of the list
template <typename T>
void Stack<T>::dump() {

    std::cout << "(";

    Node* current = head;

    if (current != NULL) 
        while (current->next != NULL) 
		{
            std::cout << current->data << ", ";
            current = current->next;
        }
        std::cout << current->data;

    std::cout << ")" << std::endl;
}


template <typename T>
typename Stack<T>::Node* Stack<T>::findByIndex(int i) {
	int counter = 0;
	Node* current = head;
	if (current != NULL) 
		for (;current->next != NULL;current=current->next) 
		{
			if(counter == i) return current;
			++counter;
		}
 return nullptr;
}

/*
 Imagine a (literal) stack of plates. If the stack gets too high, it might topple.
Therefore, in real life, we would likely start a new stack when the previous stack
exceeds some threshold. Implement a data structure SetOf Stacks that mimics
this. SetOf Stacks should be composed of several stacks and should create a
new stack once the previous one exceeds capacity. SetOf Stacks. push() and
SetOf Stacks. pop() should behave identically to a single stack (that is, popO
should return the same values as it would if there were just a single stack).

*/
template <typename T>
class SetOfStacks : public Stack<Stack<T>>
{
public:
	int max_size;
	typedef Stack<Stack<T>> Parent;
	SetOfStacks(int _max_size) :  Stack<Stack<T>>(),max_size(_max_size){}
	SetOfStacks<T>& push(const T& d);
	SetOfStacks<T>& pop();
};


template <typename T>
SetOfStacks<T>& SetOfStacks<T>::push(const T& d) 
{
	if(size == 0 || peek().size == max_size)
	{
		Stack<T> new_stack;
		Parent::push(new_stack);
	}
	peek().push(d);

	return *this;
}



template <typename T>
SetOfStacks<T>& SetOfStacks<T>::pop() 
{
	if(size != 0)
	{
		if( peek().size == 0)
			Parent::pop();
		
		else			
			peek().pop();
	} 
	return *this;
}


/*
In the classic problem of the Towers of Hanoi, you have 3 towers and N disks of
different sizes which can slide onto any tower. The puzzle starts with disks sorted
in ascending order of size from top to bottom (i.e., each disk sits on top of an
even larger one). You have the following constraints:
(1) Only one disk can be moved at a time.
(2) A disk is slid off the top of one tower onto the next tower.
(3) A disk can only be placed on top of a larger disk.
Write a program to move the disks from the first tower to the last using stacks.

*/

class Hanoi
{
public:
	Stack<int> a,b,c;
	void init();
	void log();
	Hanoi(size_t _len): len(_len)
	{ 
		if(_len < 3) throw std::domain_error("size must be 3, or bigger");
		//shift = (len%2 == 0) ? &Hanoi::shift_right :  &Hanoi::shift_left;
		fill_stack();
	}
private:
	int len;
	//void (Hanoi::*shift)(Stack<int>&,Stack<int>&, Stack<int>&);
	void shift(Stack<int>& from,Stack<int>& temp, Stack<int>& to);
	void move_tower(Stack<int>& a,Stack<int>& b , Stack<int>& c);
	bool can_move(Stack<int>& from,Stack<int>& to);
	void move(Stack<int>& from,Stack<int>& to);
	void fill_stack();

};

void Hanoi::fill_stack()
{
	for(size_t i=0;i<len;++i)
		a.push(len - i);
}

bool Hanoi::can_move(Stack<int>& from,Stack<int>& to)
{
	if(from.size == 0) return false;
	if(to.size == 0) return true;
	return  from.peek() < to.peek();
}


void Hanoi::move(Stack<int>& from,Stack<int>& to)
{
	to.push(from.peek());
	from.pop();
	//log();
}
//Could be more effective, but task requires, that "(1) Only one disk can be moved at a time."
void Hanoi::shift(Stack<int>& from,Stack<int>& temp, Stack<int>& to)
{
	move(from,temp);
	move(from,to);
	move(temp,to);
}


void Hanoi::move_tower(Stack<int>& A,Stack<int>& B , Stack<int>& C)
{
	//(*this.*shift)(A,B,C);
	shift(A,B,C);

	if(a.size == 0 && b.size == 0) return;

		 if(can_move(A,B))
			move(A,B);
		 else 
			move(B,A);	
		move_tower(C,A,B);
}

void Hanoi::log()
{

	if(a.size!=0)
	{
		std::cout<<"a:";
		a.dump();
	}
	else 
		std::cout<<"a:_"<<std::endl;

	if(b.size!=0)
	{
		std::cout<<"b:";
		b.dump();
	}
	else  
		std::cout<<"b:_"<<std::endl;

	if(c.size!=0)
	{
		std::cout<<"c:";
		c.dump();
	}
	else 
		std::cout<<"c:_"<<std::endl;
	std::cout<<std::endl;

}


void Hanoi::init()
{
	//freopen("c:/out.txt","w",stdout); //redirect long log into file if necessary	
	if(len%2==0)move_tower(a,b,c);
	else move_tower(a,c,b);
}


/*
Implement a MyQueue class which implements a queue using two stacks.
*/
class MyQueue
{
public:
	Stack<int>popped,stacked;
	MyQueue& queue(int d);
	MyQueue& dequeue();
	void dump();
	MyQueue(){}
};

MyQueue& MyQueue::queue(int d)
{	
	 stacked.push(d);
	return *this;
}

MyQueue& MyQueue::dequeue()
{
	if(popped.size == 0 && stacked.size == 0) 
		return *this;
	if(!popped.is_empty())
		popped.pop();
	else 
	{
		while(!stacked.is_empty())
		{
			popped.push(stacked.peek());
			stacked.pop();
		}
		popped.pop();
	}
	return *this;

}


void MyQueue::dump()
{
	if(popped.size > 0) popped.dump();
	if(stacked.size > 0)stacked.dump();
	std::cout<<std::endl;
}

/*
Write a program to sort a stack in ascending order (with biggest items on top).
You may use at most one additional stack to hold items, but you may not copy
the elements into any other data structure (such as an array). The stack supports
the following operations: push, pop, peek, and isEmpty
*/

template <>
Stack<int>& Stack<int>::sort() 
{
	Stack<int> holder;
	while(!is_empty())
	{
		int temp = peek();
		pop();
		while(!holder.is_empty() && temp > holder.peek() )
		{
			push(holder.peek());
			holder.pop();
		}
		holder.push(temp);
	}
	while(!holder.is_empty())
	{
		push(holder.peek());
		holder.pop();
	}
	return *this;
}

#endif